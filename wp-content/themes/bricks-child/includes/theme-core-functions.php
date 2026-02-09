<?php

if ( ! function_exists( 'remove_anonymous_object_filter' ) ) {
    /**
     * Remove an anonymous object filter.
     *
     * @param string $tag      Hook name.
     * @param string $class    Class name
     * @param string $method   Method name
     * @param int    $priority Optional. Hook priority. Defaults to 10.
     * @return bool
     */
    function remove_anonymous_object_filter($tag, $class, $method, $priority = 10) {
        if ( !isset($GLOBALS['wp_filter'][$tag][$priority]) ) {
            return false;
        }
        $filters = $GLOBALS['wp_filter'][$tag][$priority];
 
        foreach ($filters as $callback) {
            if ( is_array($callback['function'])
                && is_a( $callback['function'][0], $class )
                && $method === $callback['function'][1]
            ) {
                return remove_filter($tag, $callback['function'], $priority);
            }
        }
 
        return false;
    }
}

function is_request($type) {
    switch ( $type ) {
        case 'admin':
            return is_admin();
        case 'ajax':
            return defined( 'DOING_AJAX' );
        case 'cron':
            return defined( 'DOING_CRON' );
        case 'frontend':
            return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && !defined( 'DOING_CRON' );
    }
}

/**
 * @param $order WC order object
 * @return array recommended product
 */
function get_recommended_product_in_order($order) {

    $items = $order->get_items();

    $crossSellIds = $crossSellProducts = [];

    foreach($items as $item) {
        $crossSellIds = array_merge($item->get_product()->get_cross_sell_ids(), $crossSellIds); 
    }
    
    // Check crossSellProduct
    if( count($crossSellIds) > 3 ) {
        //unique ids in array;
        array_unique($crossSellIds);

        // Random products
        $randomKey = array_rand($crossSellIds, 3);

        $crossSellProducts = array_map(function($id) use ($crossSellIds) { 
            return wc_get_product($crossSellIds[$id]);
        }, $randomKey);

    } else {
        $crossSellProducts = array_map(function($id) { 
            return wc_get_product($id);
        }, $crossSellIds);
    }

    return $crossSellProducts;
}

/**
 * Paypay message html
 *
 * @param int $amount
 * @param string $placement
 * @return void
 */
function paypal_messages_html($amount, $placement='product') {
    ?>
        <div
              class="paypal-message sm:w-[375px]"
              data-pp-message
              data-pp-amount="<?php echo $amount; ?>"
              data-pp-layout="text"
              data-pp-placement="<?php echo $placement; ?>"
            >
        </div>
    <?php
}



/**
 * Get available coupon by user
 *
 * @param string $userEmail
 * @return WP_Query
 */
function getAvailableCouponByUser($userEmail) {
    $args = [
        'posts_per_page' => -1,
        'post_type' => 'shop_coupon',
        'meta_key' => 'customer_email',
        'orderby' => 'post_id',
        'order' => 'ASC',
        'meta_query' => [
            [
                'key' => 'customer_email',
                'value' => $userEmail,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'usage_count',
                'value' => '0',
                'compare' => '='
            ]
        ]
    ];

    return new WP_Query($args);
}

function get_return_reasons(): array
{
    $cancel_reasons = [
        "" => "Select a reason",
        "looks_different" => "Looks different to image on site",
        "parcel_damaged" => "Parcel damaged on arrival",
        "doesnt_suit" => "Doesn’t suit me",
        "late_delivery" => "Arrived too late",
        "other" => "Others",
        "poor_quality" => "Poor quality/faulty",
        "incorrect_item" => "Incorrect item received"
    ];

    return $cancel_reasons;
}

function theme_assets($path) {
    echo CHILD_ASSETS_URL . "/{$path}";
}

function wc_get_account_active_menu_item($endpoint, $wc_menu_items) {
    global $wp;

    $label = $wc_menu_items[$endpoint] ?? '';

   if ( isset($wp->query_vars['view-order']) || isset($wp->query_vars['return-order'])  ){
        $label = $wc_menu_items['orders']; // Dashboard is not an endpoint, so needs a custom check.
    }

   return $label;
}

function aftership_get_shipment($order_id): array
{
    if(!function_exists('aftership')) return [];

    $afterShip = aftership();

    return $afterShip->actions->get_tracking_items_for_display($order_id);
}

/**
 * Aggregates return data based on an order ID.
 *
 * Iterates over repeater fields in the order data to compile return quantities by item ID.
 *
 * @param int $order_id The ID of the order.
 * @return array Aggregated return data keyed by item ID.
 */
function get_aggregated_return_data($order_id) {
    $aggregated_data = array();

    // Check if the repeater field has rows of data
    if (have_rows('order_return_request', $order_id)) {
        // Loop through the rows of data
        while (have_rows('order_return_request', $order_id)) {
            the_row();
            // Check if the nested repeater field 'order_items' has rows
            if (have_rows('order_items')) {
                while (have_rows('order_items')) {
                    the_row();
                    // Retrieve item ID and quantity
                    $item_id = get_sub_field('id');
                    $item_qty = (int)get_sub_field('qty');
                    // Aggregate quantities by item ID
                    if (!isset($aggregated_data[$item_id])) {
                        $aggregated_data[$item_id] = 0;
                    }
                    $aggregated_data[$item_id] += $item_qty;
                }
            }
        }
    }
    return $aggregated_data;
}

/**
 * Determines the ineligible items in an order based on shipment data.
 *
 * @param WC_Order $order The WooCommerce order object.
 * @return array Array of ineligible item IDs.
 */
function getIneligibleItems($order): array {
    $shipment_data = $order->get_meta('_aftership_tracking_items');
    $order_items = array_flip(array_keys($order->get_items()));
    $uniqueShipmentIds = array_flip(array_reduce($shipment_data, function ($carry, $shipment) {
        foreach ($shipment['line_items'] as $line_item) {
            $carry[] = $line_item['id'];
        }
        return $carry;
    }, []));
    $unshippedItems = array_diff_key($order_items, $uniqueShipmentIds);
    $shippedOver30Days = array_reduce($shipment_data, function ($carry, $shipment) {
        $ship_date = $shipment['additional_fields']['ship_date'];
        if (floor((time() - strtotime($ship_date)) / (60 * 60 * 24)) > 360) {
            foreach ($shipment['line_items'] as $line_item) {
                $carry[] = $line_item['id'];
            }
        }
        return $carry;
    }, []);
    $shippedOver30Days = array_unique($shippedOver30Days);
    $ineligibleItems = array_merge(array_flip($unshippedItems), $shippedOver30Days);
    return array_unique($ineligibleItems);
}

/**
 * Classifies order items as eligible or ineligible for return.
 *
 * Uses order and return data to categorize each item in the order.
 *
 * @param WC_Order $order The WooCommerce order object.
 * @return array An associative array with keys 'IneligibleItem' and 'ValidForReturn'.
 */
function classifyOrderItemsForReturn($order) {
    $ineligibleItems = getIneligibleItems($order);
    $orderItems = $order->get_items();
    $returnData = get_aggregated_return_data($order->get_id());
    return array_reduce($orderItems, function ($carry, $item) use ($ineligibleItems, $returnData, $order) {
        if (in_array($item->get_id(), $ineligibleItems)) {
            $carry['IneligibleItem'][] = $item;
        } else {
            $carry['ValidForReturn'][] = formatValidReturnItem($item, $order, $returnData);
        }
        return $carry;
    }, ['IneligibleItem' => [], 'ValidForReturn' => []]);
}

/**
 * Formats an item that is valid for return.
 *
 * Extracts and formats data for an item that is eligible for return.
 *
 * @param WC_Order_Item $item The WooCommerce order item.
 * @param WC_Order $order The WooCommerce order object.
 * @param array $returnData Aggregated return data.
 * @return array Formatted item data.
 */
function formatValidReturnItem($item, $order, $returnData) {
    $product = $item->get_product();
    $product_permalink = $product ? $product->get_permalink($item) : '';
    $image = $product ? $product->get_image('order-product-item') : wp_sprintf('<img src="%s"/>', wc_placeholder_img_src('order-product-item'));
    $isVariationProduct = is_callable(array($product, 'get_variation_attributes'));
    $item_name = $isVariationProduct ? explode('-', $item->get_name())[0] : $item->get_name();
    $tax_data = $item->get_taxes();
    $tax_amount = array_sum($tax_data['total']);
    $subtotal_excluding_tax = $item->get_subtotal();
    $subtotal_formatted = $subtotal_excluding_tax;
    return [
        'id' => $item->get_id(),
        'qty' => $item->get_quantity(),
        'refundedQty' => $order->get_qty_refunded_for_item($item->get_id()),
        'permalink' => $product_permalink,
        'image' => htmlspecialchars($image, ENT_QUOTES, 'UTF-8'),
        'name' => $item_name,
        'metaData' => htmlspecialchars(wc_display_item_meta($item, ['echo' => false]), ENT_QUOTES, 'UTF-8'),
        'subTotalExcludingTax' => $subtotal_excluding_tax,
        'subTotalFormatted' => $subtotal_formatted / $item->get_quantity(),
        'taxAmount' => $tax_amount,
        "returnedQuantity" => $returnData[$item->get_id()] ?? 0,
        "selectedReturnQuantity" => 0,
        "selected" => false,
        "reason" => '',
        "feedback" => ''
    ];
}


/**
 * Retrieve a return request by its unique ID from an ACF Repeater Field.
 *
 * @param int $post_id The ID of the post containing the ACF Repeater Field.
 * @param string $id The id of the return request to retrieve.
 * @return array|false The return request array if found, otherwise false.
 */
function get_return_request_by_id($order_id, $id): bool|array
{
    // Check if ACF is active and the function get_field exists
    if (!function_exists('get_field')) {
        return false;
    }

    // Get all rows from the ACF Repeater Field
    $return_requests = get_field('order_return_request', $order_id);

    // If return_requests is an array, iterate through each request
    if (is_array($return_requests)) {
        foreach ($return_requests as $request) {
            // Check if the current row's unique_id matches the given unique_id
            if (isset($request['id']) && $request['id'] === $id) {
                // Return the matching return request
                return $request;
            }
        }
    }

    // Return false if no matching return request is found
    return false;
}

/**
 * Updates the 'email_sent' status of a specific return request within an ACF repeater field.
 *
 * This function locates a return request by its ID within a repeater field associated with an order.
 * Once found, it updates the 'email_sent' status of that particular return request.
 *
 * @param int    $order_id          The ID of the order containing the return request repeater field.
 * @param string $return_request_id The unique ID of the return request to be updated.
 *
 * @return bool True if the update is successful, false otherwise. Returns false if the ACF functions
 *              are not available, if no return requests are found, if the specific return request is
 *              not found, or if the ACF field update fails.
 */
function update_return_email_status($order_id, $return_request_id): bool
{
    // Check if ACF functions are available
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return false;
    }

    // Get all return requests
    $return_requests = get_field('order_return_request', $order_id);

    // Check if there are any return requests
    if (!is_array($return_requests) || empty($return_requests)) {
        return false;
    }

    // Find the return request with the given ID and update its 'email_sent' status
    foreach ($return_requests as $index => $request) {
        if (isset($request['id']) && $request['id'] === $return_request_id) {
            $return_requests[$index]['email_sent'] = true;

            // Update the return requests field with the updated data
            return update_field('order_return_request', $return_requests, $order_id);
        }
    }

    // Return false if no matching return request was found
    return false;
}

function apl_get_template(string $template, $constant) {
    if( !defined($constant) ) {
        define( $constant, true );
        add_action('bricks_after_site_wrapper', function () use ( $template ) {
            wc_get_template( $template );
        });
    }
}






