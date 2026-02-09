/**
 * Class representing a handler for approving returns.
 */
class ApproveReturnHandler {
    /**
     * Create an ApproveReturnHandler.
     * @param {Object} $ - The jQuery object.
     */
    constructor($) {
        this.$ = $;
        this.ajaxurl = ajaxurl; // ajaxurl is defined by WordPress
    }

    /**
     * Add an approve return button to a row if it doesn't already exist.
     * @param {Object} $row - The row to add the button to.
     */
    addApproveReturn($row) {
        if (this.doesButtonExist($row)) {
            return;
        }

        // Get the fields from the row
        const packageLabelField = acf.getField($row.find('[data-name="package_label"]'));
        const statusField = acf.getField($row.find('[data-name="status"]'));
        const idField = acf.getField($row.find('[data-name="id"]'));
        const rowIndex = $row.index();
        const orderId = $row.closest('form').find('input[name="post_ID"]').val();

        // Create the button and add it to the row
        const $button = this.createButton(orderId, rowIndex, packageLabelField, statusField, idField);
        $row.find('[data-name="package_label"] .acf-input').css({
            display: 'flex',
            gap: '10px',
        }).append($button);
    }

    /**
     * Check if a button already exists in a row.
     * @param {Object} $row - The row to check.
     * @return {boolean} True if the button exists, false otherwise.
     */
    doesButtonExist($row) {
        return $row.find('.approve-return-button').length > 0;
    }

    /**
     * Create an approve return button.
     * @param {string} orderId - The order ID.
     * @param {number} rowIndex - The index of the row.
     * @param {Object} packageLabelField - The package label field.
     * @param {Object} statusField - The status field.
     * @param {Object} idField - The ID field.
     * @return {Object} The created button.
     */
    createButton(orderId, rowIndex, packageLabelField, statusField, idField) {
        return this.$('<button/>', {
            type: 'submit',
            class: 'button button-primary approve-return-button',
            html: 'Approve Return' + this.$('<span/>', { class: 'spinner' }).prop('outerHTML'),
            click: (e) => {
                e.preventDefault();
                const $button = this.$(e.currentTarget);
                $button.find('.spinner').addClass('is-active');
                $button.prop('disabled', true);
                this.sendAjaxRequest(orderId, rowIndex, packageLabelField, statusField, idField, $button);
            }
        });
    }

    /**
     * Send an AJAX request to approve a return.
     * @param {string} orderId - The order ID.
     * @param {number} rowIndex - The index of the row.
     * @param {Object} packageLabelField - The package label field.
     * @param {Object} statusField - The status field.
     * @param {Object} idField - The ID field.
     * @param {Object} $button - The button that was clicked.
     */
    sendAjaxRequest(orderId, rowIndex, packageLabelField, statusField, idField, $button) {
        const fieldData = {
            id: {
                key: idField.data.key, // Make sure this is the correct way to get the key
                value: idField.val()
            },
            package_label: {
                key: packageLabelField.data.key,
                value: packageLabelField.val()
            },
            status: {
                key: statusField.data.key,
                value: statusField.val()
            }
        };

        this.$.ajax({
            url: this.ajaxurl,
            type: 'POST',
            data: {
                action: 'approve_return',
                order_id: orderId,
                fields: JSON.stringify(fieldData),
                row_index: rowIndex,
            },
            success: (response) => {
                console.log(response);
            },
            error: () => {
                console.log('AJAX error');
            },
            complete: () => {
                $button.find('.spinner').removeClass('is-active');
                $button.prop('disabled', false);
            }
        });
    }

    /**
     * Initialize the ApproveReturnHandler.
     */
    init() {
        acf.add_action('ready append', ($el) => {
            this.$('[data-name="order_return_request"] .acf-row').each((index, element) => {
                this.addApproveReturn(this.$(element));
            });
        });
    }
}

// Create a new ApproveReturnHandler and initialize it
(new ApproveReturnHandler(jQuery)).init();