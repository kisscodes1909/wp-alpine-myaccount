<div class="mt-20 md:mt-12">
    <?php if (have_rows('order_return_request', $order->get_id())) : ?>
        <div class="your-custom-class">
            <?php
            wc_get_template('myaccount/page-heading.php', ['page_heading' => 'Returns in Progress', 'page_description' => 'Track your return requests']);

            while (have_rows('order_return_request', $order->get_id())) {
                the_row();
                $request_index = get_row_index();
                $request_status = get_sub_field('status');
                $package_label = get_sub_field('package_label');
                ?>
                <div class="md:container mx-auto px-8 leading-tight mt-14">
                    <span class="text-base md:text-xl mb-3 block"><?php echo wp_sprintf('Return #%d', $request_index); ?></span>
                </div>

                <?php
                if (have_rows('order_items')) {
                    $list_items = [];
                    while (have_rows('order_items')) {
                        the_row();
                        $item_id = get_sub_field('id');
                        $item_qty = get_sub_field('qty');
                        $list_items[$item_id] = $order_items[$item_id];
                        $list_items[$item_id]->set_quantity($item_qty);
                    }

                    wc_get_template('woocommerce/order/order-details-list-item.php', [
                        'order_items' => $list_items,
                        'order' => $order,
                    ]);
                }
                ?>

                <div class="md:container mx-auto px-8 leading-tight mt-12 space-y-6 text-center md:text-left">
                    <?php if ($package_label) : ?>
                        <a target="_blank" href="<?php echo $package_label; ?>" class="button slim w-[200px] md:w-[300px]">Print Label</a>
                    <?php endif; ?>

                    <?php if ($request_status['value'] == 'processing') : ?>
                        <p class="text-sm">Your return request is processing. You will receive an email with your return instructions within 24-48 hours.</p>
                    <?php endif; ?>
                </div>
            <?php } ?>
        </div> <!-- Close your-custom-class div -->
    <?php endif; ?>
</div>
