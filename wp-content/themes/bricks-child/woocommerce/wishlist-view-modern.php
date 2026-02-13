<?php
/**
 * Wishlist page template - Modern layout
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist\View
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist                      \YITH_WCWL_Wishlist Current wishlist
 * @var $wishlist_items                array Array of items to show for current page
 * @var $is_default                    bool Whether current wishlist is default
 * @var $wishlist_token                string Current wishlist token
 * @var $wishlist_id                   int Current wishlist id
 * @var $users_wishlists               array Array of current user wishlists
 * @var $page_title                    string Page title
 * @var $pagination                    string yes/no
 * @var $per_page                      int Items per page
 * @var $current_page                  int Current page
 * @var $page_links                    array Array of page links
 * @var $is_user_owner                 bool Whether current user is wishlist owner
 * @var $show_price                    bool Whether to show price column
 * @var $show_dateadded                bool Whether to show item date of addition
 * @var $show_stock_status             bool Whether to show product stock status
 * @var $show_add_to_cart              bool Whether to show Add to Cart button
 * @var $show_remove_product           bool Whether to show Remove button
 * @var $show_price_variations         bool Whether to show price variation over time
 * @var $show_variation                bool Whether to show variation attributes when possible
 * @var $show_cb                       bool Whether to show checkbox column
 * @var $show_quantity                 bool Whether to show input quantity or not
 * @var $show_ask_estimate_button      bool Whether to show Ask an Estimate form
 * @var $show_last_column              bool Whether to show last column (calculated basing on previous flags)
 * @var $move_to_another_wishlist      bool Whether to show Move to another wishlist select
 * @var $move_to_another_wishlist_type string Whether to show a select or a popup for wishlist change
 * @var $additional_info               bool Whether to show Additional info textarea in Ask an estimate form
 * @var $price_excl_tax                bool Whether to show price excluding taxes
 * @var $enable_drag_n_drop            bool Whether to enable drag n drop feature
 * @var $repeat_remove_button          bool Whether to repeat remove button in last column
 * @var $available_multi_wishlist      bool Whether multi wishlist is enabled and available
 * @var $form_action                   string Action for the wishlist form
 * @var $no_interactions               bool
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<!-- WISHLIST GRID -->
<ul
	class="space-y-12  wishlist_view shop_table cart list-none m-0 p-0 responsive <?php echo $no_interactions ? 'no-interactions' : ''; ?> <?php echo $enable_drag_n_drop ? 'sortable' : ''; ?>"
	data-pagination="<?php echo esc_attr( $pagination ); ?>" data-per-page="<?php echo esc_attr( $per_page ); ?>" data-page="<?php echo esc_attr( $current_page ); ?>"
	data-id="<?php echo esc_attr( $wishlist_id ); ?>" data-token="<?php echo esc_attr( $wishlist_token ); ?>">

	<?php
	if ( $wishlist && $wishlist->has_items() ) :
		foreach ( $wishlist_items as $item ) :
			/**
			 * Each of wishlist items
			 *
			 * @var $item \YITH_WCWL_Wishlist_Item
			 */
			global $product;

			$product = $item->get_product();

			if ( $product && $product->exists() ) :
				?>
				<li id="yith-wcwl-row-<?php echo esc_attr( $item->get_product_id() ); ?>" data-row-id="<?php echo esc_attr( $item->get_product_id() ); ?>">
					<div class="item-wrapper flex gap-8">
						<div class="product-thumbnail overflow-hidden">
							<?php if ( $show_cb ) : ?>
								<div class="product-checkbox">
									<input type="checkbox" value="yes" name="items[<?php echo esc_attr( $item->get_product_id() ); ?>][cb]"/>
								</div>
							<?php endif ?>

							<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>">
								<?php echo woocommerce_get_product_thumbnail('order-product-item'); ?>
							</a>
						</div>
						<div class="w-[calc(100%-1rem-130px)] item-details">
							<div class="item-details-wrapper lg:flex lg:flex-row lg:justify-between">
								<div class="space-1 md:space-y-3">

                                    <h3 class="product-name text-lg md:text-2xl font-normal leading-tight text-gray-900"><?php echo wp_kses_post( apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ); ?></h3>

                                    <?php
                                    /**
                                     * DO_ACTION: yith_wcwl_table_after_product_name
                                     *
                                     * Allows to render some content or fire some action after the product name in the wishlist table.
                                     *
                                     * @param YITH_WCWL_Wishlist_Item $item Wishlist item object
                                     */
                                    do_action( 'yith_wcwl_table_after_product_name', $item );
                                    ?>

                                    <?php if ( $show_variation || $show_dateadded || $show_price || $show_quantity || $show_stock_status ) : ?>
                                        <div class="item-details-table flex flex-col space-1 md:space-y-3">

                                            <div class="flex flex-row text-sm md:text-base text-gray-600">
                                            <?php if ( $show_variation && $product->is_type( 'variation' ) ) : ?>
                                                <?php
                                                /**
                                                 * Product object representing variation for current item
                                                 *
                                                 * @var $product \WC_Product_Variation
                                                 */
                                                $attributes = $product->get_attributes();


                                                if ( ! empty( $attributes ) ) :


                                                    $attributesName = array_map('ucfirst', array_values($attributes));


                                                   echo wp_sprintf("<span >%s</span>", implode(' | ', $attributesName));
                                                endif;
                                                ?>
                                            <?php endif; ?>
                                            </div>

                                            <?php if ( $show_dateadded && $item->get_date_added() ) : ?>
                                                <tr class="date-added">
                                                    <td class="label">
                                                        <?php esc_html_e( 'Added on:', 'yith-woocommerce-wishlist' ); ?>
                                                    </td>
                                                    <td class="value">
                                                        <?php echo '<span class="dateadded">' . esc_html( $item->get_date_added_formatted() ) . '</span>'; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <?php if ( $show_price || $show_price_variations ) : ?>
                                               <strong class="text-base md:text-lg font-semibold text-gray-900 leading-tight">
                                                   <?php
                                                       if ( $show_price ) {
                                                           echo wp_kses_post( $item->get_formatted_product_price() );
                                                       }

                                                       if ( $show_price_variations ) {
                                                           echo wp_kses_post( $item->get_price_variation() );
                                                       }
                                                   ?>
                                               </strong>
                                            <?php endif; ?>

<!--                                            --><?php //if ( $show_stock_status ) : ?>
<!--                                                <span>-->
<!--                                                    --><?php //echo 'out-of-stock' === $item->get_stock_status() ? '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of stock', 'yith-woocommerce-wishlist' ) . '</span>' : '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'yith-woocommerce-wishlist' ) . '</span>'; ?>
<!--                                                </span>-->
<!--                                            --><?php //endif; ?>

                                        </div>
                                    <?php endif; ?>
                                </div>

								<div class="flex flex-row-reverse items-center gap-10 justify-end lg:m-0 mt-3">
                                    <?php if ( $show_add_to_cart && $item->is_purchasable() && 'out-of-stock' !== $item->get_stock_status() ) : ?>
                                        <div class="product-add-to-cart">
                                            <?php woocommerce_template_loop_add_to_cart( array(
                                                    'quantity' => $show_quantity ? $item->get_quantity() : 1 ,
                                                    'class' => implode(
                                                        ' ',
                                                        array_filter(
                                                            array(
                                                                'button',
                                                                'slim',
                                                                wc_wp_theme_get_element_class_name( 'button' ), // escaped in the template.
                                                                'product_type_' . $product->get_type(),
                                                                $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                                                                $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
                                                            )
                                                        )
                                                    )
                                            ) ); ?>
                                        </div>
                                    <?php endif ?>

                                    <?php if ( $move_to_another_wishlist && $available_multi_wishlist && count( $users_wishlists ) > 1 ) : ?>
                                        <div class="move-to-another-wishlist">
                                            <?php if ( 'select' === $move_to_another_wishlist_type ) : ?>
                                                <select class="change-wishlist selectBox">
                                                    <option value=""><?php esc_html_e( 'Move', 'yith-woocommerce-wishlist' ); ?></option>
                                                    <?php
                                                    foreach ( $users_wishlists as $wl ) :
                                                        /**
                                                         * Each of customer wishlists
                                                         *
                                                         * @var $wl \YITH_WCWL_Wishlist
                                                         */
                                                        if ( $wl->get_token() === $wishlist_token ) {
                                                            continue;
                                                        }
                                                        ?>
                                                        <option value="<?php echo esc_attr( $wl->get_token() ); ?>">
                                                            <?php echo esc_html( sprintf( '%s - %s', $wl->get_formatted_name(), $wl->get_formatted_privacy() ) ); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else : ?>
                                                <a href="#move_to_another_wishlist" class="move-to-another-wishlist-button" data-rel="prettyPhoto[move_to_another_wishlist]">
                                                    <?php
                                                    /**
                                                     * APPLY_FILTERS: yith_wcwl_move_to_another_list_label
                                                     *
                                                     * Filter the label to move the product to another wishlist.
                                                     *
                                                     * @param string $label Label
                                                     *
                                                     * @return string
                                                     */
                                                    echo esc_html( apply_filters( 'yith_wcwl_move_to_another_list_label', __( 'Move to another list &rsaquo;', 'yith-woocommerce-wishlist' ) ) );
                                                    ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( $show_remove_product ) : ?>
                                            <?php
                                            /**
                                             * APPLY_FILTERS: yith_wcwl_remove_product_wishlist_message_title
                                             *
                                             * Filter the title of the button to remove the product from the wishlist.
                                             *
                                             * @param string $title Button title
                                             *
                                             * @return string
                                             */
                                        ?>
                                            <a class="remove_from_wishlist" href="<?php echo esc_url( $item->get_remove_url() ); ?>" title="<?php echo esc_attr( apply_filters( 'yith_wcwl_remove_product_wishlist_message_title', __( 'Remove this product', 'yith-woocommerce-wishlist' ) ) ); ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </a>
                                    <?php endif; ?>
                                </div>

								<?php if ( $enable_drag_n_drop ) : ?>
									<input type="hidden" name="items[<?php echo esc_attr( $item->get_product_id() ); ?>][position]" value="<?php echo esc_attr( $item->get_position() ); ?>"/>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</li>
				<?php
			endif;
		endforeach;
	else :
		?>
		<li class="wishlist-empty">
			<?php
			/**
			 * APPLY_FILTERS: yith_wcwl_no_product_to_remove_message
			 *
			 * Filter the message shown when there are no products in the wishlist.
			 *
			 * @param string $message Message
			 *
			 * @return string
			 */
			echo esc_html( apply_filters( 'yith_wcwl_no_product_to_remove_message', __( 'No products added to the wishlist', 'yith-woocommerce-wishlist' ) ) );
			?>
		</li>
	<?php endif; ?>
</ul>

<?php if ( ! empty( $page_links ) ) : ?>
	<nav class="wishlist-pagination">
		<?php echo wp_kses_post( $page_links ); ?>
	</nav>
<?php endif; ?>
