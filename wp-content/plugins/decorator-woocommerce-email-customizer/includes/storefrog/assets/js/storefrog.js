/**
 *  Storefrog connector script.
 *
 *  @package Storefrog_Connector
 *  @since 2.0.0
 */

/* Create global `storefrogDataObject` from localized data. */
window.storefrogDataObject = wbte_sf_script_params.storefrogDataObject;

/* Create an object to store cart changes */
var wbteSfDataObject = {
	"cart": {
		"items": {},
		"hash": window.storefrogDataObject.cart.hash
	},
	"user": window.storefrogDataObject.user
};

/* Fetch data object via ajax. And update it to  */
function getStorefrogDataObject( forceLoadTable = false ) {
	jQuery.ajax(
		{
			url: wbte_sf_script_params.home_url + '/?wc-ajax=get_storefrog_data_object&nonce=' + wbte_sf_script_params.nonce,
			type:'GET',
			dataType:'json',
			success:function (data) {
				if (data.success) {
					wbteSfDataObject = data.data;
					wbteSfDataObjectUpdate(forceLoadTable);
				}
			}
		}
	);
}

/* Populate the cart updates to global object */
function wbteSfDataObjectUpdate(forceLoadTable = false) {

	let tempObj = null;
	if (forceLoadTable) {
		tempObj = window.storefrogDataObject;
		tempObj.cart.table = wbteSfDataObject.cart.table;
	}else{
		/* Create a deep copy of the original object. */
		 tempObj = JSON.parse(JSON.stringify(wbteSfDataObject));
	}

	/* Clear the array keys. */
	tempObj.cart.items = Object.values( tempObj.cart.items );

	/* Add this object to the local storage for other tabs. */
	localStorage.setItem( 'wbteSfDataObject', JSON.stringify( tempObj ) );

	/* Update to global object for current tab. */
	window.storefrogDataObject = tempObj;
}


/* For block cart and checkout pages. */
document.addEventListener(
	'DOMContentLoaded',
	function () {
		const registerCheckoutFilters = window?.wc?.blocksCheckout?.registerCheckoutFilters;

		if (typeof registerCheckoutFilters !== "function") {
            return;
        }

		var wbteDataObjectUpdateTmr = null;

		registerCheckoutFilters(
			'wbte-storefrog',
			{
				cartItemPrice: (value, extensions, args) => {

					clearTimeout( wbteDataObjectUpdateTmr );
					wbteDataObjectUpdateTmr = setTimeout(
						function () {
							wbteDataObjectUpdateTmr = null;
							getStorefrogDataObject();
						},
						2000
					);

				return value;
				}
			}
		);

	}
);


/* For classic cart and checkout pages. */
jQuery( document ).ready(
	function () {
		/* Watch cart change */
		jQuery( 'body' ).on(
			'updated_wc_div updated_checkout added_to_cart removed_from_cart wc-blocks_added_to_cart',
			function () {
				getStorefrogDataObject();
			}
		);
	}
);


/* Watch storage change for non active browser tabs. This section is used to sync the data between tabs */
window.addEventListener(
	'storage',
	( event ) => {
		if ( 'wbteSfDataObject' === event.key ) {
			window.storefrogDataObject = JSON.parse( event.newValue );
		} else if ( event.key.indexOf( 'wc_cart_hash_' ) === 0 ) {
			window.storefrogDataObject.cart.hash = event.newValue;
		}
	}
);
