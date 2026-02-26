<?php

class Adress_Book {
    public function __construct()
    {
        $this->add_rewrite_address_endpoint();
        add_filter('woocommerce_account_address_endpoint', array($this, 'address_endpoint_content'));
        add_filter('woocommerce_get_query_vars', [$this, 'add_address_queryvar']);
        add_filter('woocommerce_account_menu_items', [$this, 'added_address_menu_item']);
    }

    public function add_rewrite_address_endpoint() {
        add_rewrite_endpoint('address', EP_ROOT | EP_PAGES);
        flush_rewrite_rules(); // This might be needed when adding endpoints programmatically
    }

    public function address_endpoint_content($content) {

        $countries = WC()->countries->get_shipping_countries();

        $serialized_data = get_user_meta(get_current_user_id(), 'address_book', true);
        $address_book = maybe_unserialize($serialized_data);

        wc_get_template('/myaccount/apl-address.php');
        wc_get_template('/myaccount/ma-form-edit-address.php');


        $this->enqueue_scripts($address_book, $countries);
    }

    public function add_address_queryvar($vars) {
        $vars['address'] = 'address';
        return $vars;
    }

    function added_address_menu_item($items) {
        $items['address'] = 'Address Book';
        return $items;
    }

    function enqueue_scripts($addresses, $countries): void
    {
        $aafw_google_api_key = 'AIzaSyD-42Ska0L9w12EoymnnOFAPaF5uCdiPgU';
        $language = 'en';

        wp_enqueue_script('address-book', CHILD_URL . '/assets/js/address-book.js', array('alpine-bundle'), filemtime(CHILD_DIR . '/assets/js/address-book.js'), true);
        wp_localize_script('alpine-bundle', 'scriptData', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'addresses' => $addresses,
                'countries' => $countries,
                'nonce' => wp_create_nonce('save_address_nonce')
        ]);
        // Places API (New): load without legacy libraries; places loaded via importLibrary() in JS
        wp_enqueue_script(
            'address-googleapis',
            'https://maps.googleapis.com/maps/api/js?key=' . $aafw_google_api_key . '&language=' . $language . '&loading=async',
            array( 'address-book' ),
            '1.0',
            true
        );
    }
}

new Adress_Book();