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
						<div class="product-thumbnail rounded-lg overflow-hidden">
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

                                    <h3 class="product-name text-sm sm:text-xl"><?php echo wp_kses_post( apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ); ?></h3>

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

                                            <div class="flex flex-row text-sm sm:text-xl">
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
                                               <strong class="text-sm sm:text-xl">
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

<!--                                            --><?php //if ( $show_quantity ) : ?>
<!--                                                <span class="w-[80px] inline-block text-sm sm:text-xl">-->
<!--                                                    --><?php //if ( ! $no_interactions && $wishlist->current_user_can( 'update_quantity' ) ) : ?>
<!--                                                        <input class="rounded-full"  type="number" min="1" step="1" name="items[--><?php //echo esc_attr( $item->get_product_id() ); ?><!--][quantity]" value="--><?php //echo esc_attr( $item->get_quantity() ); ?><!--"/>-->
<!--                                                    --><?php //else : ?>
<!--                                                        --><?php //echo esc_html( $item->get_quantity() ); ?>
<!--                                                    --><?php //endif; ?>
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
                                            <a class="inline-block w-[41px] h-[42px]" href="<?php echo esc_url( $item->get_remove_url() ); ?>" class="remove_from_wishlist" title="<?php echo esc_attr( apply_filters( 'yith_wcwl_remove_product_wishlist_message_title', __( 'Remove this product', 'yith-woocommerce-wishlist' ) ) ); ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="41" height="42" viewBox="0 0 41 42" fill="none">
                                                    <rect y="0.960693" width="40.0785" height="40.0785" rx="5" fill="black" fill-opacity="0.05"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0624 12.2559H10.9292C10.446 12.2559 9.98265 12.4286 9.641 12.7361C9.29936 13.0436 9.10742 13.4606 9.10742 13.8955C9.10742 14.3303 9.29936 14.7473 9.641 15.0548C9.98265 15.3623 10.446 15.535 10.9292 15.535H11.84V29.7447C11.8403 30.3245 12.0963 30.8804 12.5517 31.2903C13.0072 31.7002 13.6249 31.9306 14.2691 31.9308H25.8068C26.4509 31.9306 27.0686 31.7002 27.5241 31.2903C27.9796 30.8804 28.2356 30.3245 28.2358 29.7447V15.535H29.1467C29.6298 15.5349 30.0931 15.3621 30.4347 15.0546C30.7763 14.7472 30.9683 14.3303 30.9684 13.8955C30.9683 13.4607 30.7763 13.0437 30.4347 12.7363C30.0931 12.4288 29.6298 12.2561 29.1467 12.2559H23.0135C22.8733 11.6387 22.5006 11.0841 21.9584 10.6857C21.4163 10.2874 20.7378 10.0698 20.0379 10.0698C19.338 10.0698 18.6596 10.2874 18.1174 10.6857C17.5752 11.0841 17.2025 11.6387 17.0624 12.2559ZM27.0213 15.535V29.7447C27.0212 30.0346 26.8932 30.3126 26.6655 30.5175C26.4377 30.7225 26.1289 30.8377 25.8068 30.8378H14.2691C13.947 30.8377 13.6381 30.7225 13.4104 30.5175C13.1827 30.3126 13.0547 30.0346 13.0545 29.7447V15.535H27.0213ZM19.4307 18.2677V28.1052C19.4307 28.2501 19.4947 28.3891 19.6085 28.4916C19.7224 28.5941 19.8769 28.6517 20.0379 28.6517C20.199 28.6517 20.3534 28.5941 20.4673 28.4916C20.5812 28.3891 20.6452 28.2501 20.6452 28.1052V18.2677C20.6452 18.1227 20.5812 17.9837 20.4673 17.8812C20.3534 17.7787 20.199 17.7212 20.0379 17.7212C19.8769 17.7212 19.7224 17.7787 19.6085 17.8812C19.4947 17.9837 19.4307 18.1227 19.4307 18.2677ZM15.1799 18.2677V28.1052C15.1799 28.2501 15.2439 28.3891 15.3578 28.4916C15.4717 28.5941 15.6261 28.6517 15.7872 28.6517C15.9482 28.6517 16.1027 28.5941 16.2166 28.4916C16.3304 28.3891 16.3944 28.2501 16.3944 28.1052V18.2677C16.3944 18.1227 16.3304 17.9837 16.2166 17.8812C16.1027 17.7787 15.9482 17.7212 15.7872 17.7212C15.6261 17.7212 15.4717 17.7787 15.3578 17.8812C15.2439 17.9837 15.1799 18.1227 15.1799 18.2677ZM23.6814 18.2677V28.1052C23.6814 28.2501 23.7454 28.3891 23.8593 28.4916C23.9732 28.5941 24.1276 28.6517 24.2887 28.6517C24.4497 28.6517 24.6042 28.5941 24.7181 28.4916C24.832 28.3891 24.8959 28.2501 24.8959 28.1052V18.2677C24.8959 18.1227 24.832 17.9837 24.7181 17.8812C24.6042 17.7787 24.4497 17.7212 24.2887 17.7212C24.1276 17.7212 23.9732 17.7787 23.8593 17.8812C23.7454 17.9837 23.6814 18.1227 23.6814 18.2677ZM29.1467 14.442H10.9292C10.7682 14.4417 10.6139 14.3841 10.4998 14.2819C10.3875 14.1784 10.3238 14.0401 10.3219 13.8955C10.322 13.7505 10.386 13.6116 10.4999 13.5091C10.6137 13.4066 10.7681 13.349 10.9292 13.3489H29.1467C29.3077 13.3489 29.4622 13.4065 29.5761 13.509C29.69 13.6115 29.7539 13.7505 29.7539 13.8955C29.7539 14.0404 29.69 14.1794 29.5761 14.2819C29.4622 14.3844 29.3077 14.442 29.1467 14.442ZM21.7558 12.2559C21.6302 11.9362 21.3975 11.6594 21.0899 11.4637C20.7822 11.2679 20.4147 11.1628 20.0379 11.1628C19.6612 11.1628 19.2936 11.2679 18.986 11.4637C18.6783 11.6594 18.4457 11.9362 18.32 12.2559H21.7558Z" fill="black"/>
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
