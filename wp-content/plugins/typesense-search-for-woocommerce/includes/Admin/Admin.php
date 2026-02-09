<?php

namespace Codemanas\Typesense\WooCommerce\Admin;

use Codemanas\Typesense\Main\TypesenseAPI;
use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;


class Admin
{
    public static ?Admin $_instance = null;

    /**
     * @return Admin|null
     */
    public static function getInstance(): ?Admin
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self:: $_instance;
    }

    /**
     * pluginName constructor.
     */
    public function __construct()
    {
        $hierarchical_settings = Fields::get_option('hierarchical_settings');
        $is_hierarchical_menu_enabled = $hierarchical_settings['make_category_hierarchical_menu'] ?? false;

        if ($is_hierarchical_menu_enabled) {
            HierarchicalMenu::get_instance();
        }

        //add_action( 'admin_menu', [ $this, 'add_admin_menu_page' ] );
        add_action('admin_init', [$this, 'handle_submit']);
        add_action('wp_ajax_tsfwc_update_attributes', [$this, 'update_attributes_handler']);
    }

    /**
     * @throws \JsonException
     */
    public function update_attributes_handler(): void
    {
        check_ajax_referer('tsfwc-attributes-nonce', 'security');

        $taxonomy = filter_input(INPUT_POST, 'taxonomy');
        //run only if slugs do not match
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]);


        if (is_array($terms) && !empty($terms)) {
            $term_ids = wp_list_pluck($terms, 'term_id');
            if (!empty($term_ids)) {
                $products = get_posts([
                        'post_type' => 'product',
                        'post_status' => 'publish',
                        'tax_query' => [
                                [
                                        'taxonomy' => $taxonomy,
                                        'field' => 'id',
                                        'terms' => $term_ids,
                                ],
                        ],
                        'posts_per_page' => -1,
                ]);

                if (count($products) > 0) :
                    $documents = [];
                    foreach ($products as $product) {
                        if (apply_filters('cm_typesense_bulk_import_skip_post', false, $product)) {
                            //if you want to delete on bulk import you can
                            do_action('cm_typesense_bulk_import_on_post_skipped', $product);
                            continue;
                        }
                        $documents[] = TypesenseAPI::getInstance()->formatDocumentForEntry($product, $product->ID, $product->post_type);
                    }

                    if (!empty($documents)) {
                        TypesenseAPI::getInstance()->bulkUpsertDocuments('product', $documents);

                    }
                endif;
            }
        }


        wp_send_json('Success');
    }

    public function add_admin_menu_page(): void
    {
        add_submenu_page('codemanas-typesense',
                __('WooCommerce', 'typesense-search-for-woocommerce'),
                __('WooCommerce', 'typesense-search-for-woocommerce'),
                'manage_options',
                'typesense-woocommerce',
                [$this, 'setting_page_cb']);
    }


    public function get_all_category_heirarchy(): array
    {
        $all_categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
        $ancestors_arr = [];
        $max_cat_level = 0;
        foreach ($all_categories as $category) {
            if ($category->parent == 0) {
                $ancestors_arr[$category->term_id] = [$category->name];
                continue;
            }

            //https://developer.wordpress.org/reference/functions/get_ancestors/#return
            $ancestors = array_reverse(get_ancestors($category->term_id, 'product_cat', 'taxonomy'));

            $temp_array = [];
            $hierarchical_structure = '';
            $count = 0;
            //digamber rewriting the logic
            foreach ($ancestors as $ancestor) {
                $parent_term = get_term($ancestor, 'product_cat');
                $hierarchical_structure = ($count == 0) ? $parent_term->name : $hierarchical_structure . ' > ' . $parent_term->name;
                $temp_array[] = $hierarchical_structure;
                $count++;
            }
            //after end add the last value
            $temp_array[] = $hierarchical_structure . ' > ' . $category->name;

            //always +1 since ancestors dont account for current category
            $current_depth = count($ancestors) + 1;
            if ($current_depth > $max_cat_level) {
                $max_cat_level = $current_depth;
            }

            $ancestors_arr[$category->term_id] = $temp_array;
        }

        $data['max_cat_level'] = $max_cat_level;
        $data['hierarchical_cats'] = $ancestors_arr;

        return $data;
    }

    public function save_settings($settings): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $global_settings = [
            'replace_shop' => $settings['replace_shop'],
            'show_cat' => $settings['show_cat'],
            'routing' => $settings['routing'],
            'show_price' => $settings['show_price'],
            'show_rating' => $settings['show_rating'],
            'show_attr' => $settings['show_attr'],
            'show_pagination' => $settings['show_pagination'],
            'show_more_text' => $settings['show_more_text'],
            'pagination_type' => $settings['pagination_type'],
            'show_sortby' => $settings['show_sortby'],
            'default_sort_by' => $settings['default_sort_by'],
            'show_featured_first' => $settings['show_featured_first'],
            'search_placeholder' => $settings['search_placeholder'] ?? __('Search products', 'typesense-search-for-woocommerce'),
            'replace_product_search' => $settings['replace_product_search'],
            'autocomplete_submit_action' => $settings['autocomplete_submit_action'],
        ];

        //backward compatibility settigns validation
        $global_settings['replace_shop'] = (isset($global_settings['replace_shop']) && $global_settings['replace_shop'] == 'true') ? true : $global_settings['replace_shop'];
        $global_settings['show_cat'] = (isset($global_settings['show_cat']) && $global_settings['show_cat'] == 'true') ? true : $global_settings['show_cat'];

        Fields::set_option('global_setting', $global_settings);

        //set here first so we can make it false if saved as false.
        // Save hierarchical settings in different row to not cluster with other settings.
        $hierarchical_settings['make_category_hierarchical_menu'] = isset($settings['make_category_hierarchical_menu']) && (true == $settings['make_category_hierarchical_menu'] || "true" == $settings['make_category_hierarchical_menu']);


        if (isset($settings['make_category_hierarchical_menu']) && (true == $settings['make_category_hierarchical_menu'] || 'true' == $settings['make_category_hierarchical_menu'])) {

            $hierarchical_cats_data = $this->get_all_category_heirarchy();
            $hierarchical_settings['hierarchical_cats_data'] = $hierarchical_cats_data;
        }

        Fields::set_option('hierarchical_settings', $hierarchical_settings);
    }

    public function handle_submit(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (!isset($_POST['cm_typesense_woo_nonce']) || !wp_verify_nonce($_POST['cm_typesense_woo_nonce'], 'verify_cm_typesense_woo_nonce')) {
            return;
        }
        $this->save_settings($_POST);

    }

    public function setting_page_cb(): void
    {
        $global_settings = Fields::get_option('global_setting');
        $replace_shop = (isset($global_settings['replace_shop'])) ? $global_settings['replace_shop'] : true;
        $show_cat = (isset($global_settings['show_cat'])) ? $global_settings['show_cat'] : true;
        $show_price = (isset($global_settings['show_price'])) ? $global_settings['show_price'] : true;
        $show_rating = (isset($global_settings['show_rating'])) ? $global_settings['show_rating'] : true;
        $show_attr = (isset($global_settings['show_attr'])) ? $global_settings['show_attr'] : true;
        $show_pagination = (isset($global_settings['show_pagination'])) ? $global_settings['show_pagination'] : true;
        $pagination_type = (isset($global_settings['pagination_type'])) ? $global_settings['pagination_type'] : 'default';
        $show_more_text = (isset($global_settings['show_more_text'])) ? $global_settings['show_more_text'] : __('Show More', 'typesense-search-for-woocommerce');

        $show_sortby = (isset($global_settings['show_sortby'])) ? $global_settings['show_sortby'] : true;
        $default_sort_by = (isset($global_settings['default_sort_by'])) ? $global_settings['default_sort_by'] : 'recent';
        $show_featured_first = (isset($global_settings['show_featured_first'])) ? $global_settings['show_featured_first'] : false;
        $search_placeholder = (isset($global_settings['search_placeholder'])) ? $global_settings['search_placeholder'] : __('Search products', 'typesense-search-for-woocommerce');
        $replace_product_search = (isset($global_settings['replace_product_search'])) ? $global_settings['replace_product_search'] : 'autocomplete';
        $hierarchical_settings = Fields::get_option('hierarchical_settings');
        $make_category_hierarchical_menu = (isset($hierarchical_settings['make_category_hierarchical_menu'])) ? $hierarchical_settings['make_category_hierarchical_menu'] : false;
        $autocomplete_submit_action = (isset($global_settings['autocomplete_submit_action'])) ? $global_settings['autocomplete_submit_action'] : 'keep_open';

        $sort_by_options = [
                'recent' => __('Recent', 'typesense-search-for-woocommerce'),
                'oldest' => __('Oldest', 'typesense-search-for-woocommerce'),
                'sort_by_rating_low_to_high' => __('Sort by rating: low to high', 'typesense-search-for-woocommerce'),
                'sort_by_rating_high_to_low' => __('Sort by rating: high to low', 'typesense-search-for-woocommerce'),
                'sort_by_price_low_to_high' => __('Sort by price: low to high', 'typesense-search-for-woocommerce'),
                'sort_by_price_high_to_low' => __('Sort by price: high to low', 'typesense-search-for-woocommerce'),
                'sort_by_popularity' => __('Sort by popularity', 'typesense-search-for-woocommerce'),
        ];

        $pagination_type_options = [
                'default' => __('Default', 'typesense-search-for-woocommerce'),
                'infinite' => __('Infinite ( with button )', 'typesense-search-for-woocommerce'),
        ];

        ?>
        <div id="wrap">
            <h2><?php _e('WooCommerce Settings', 'typesense-search-for-woocommerce'); ?></h2>
            <h3><?php _e('Global Settings', 'typesense-search-for-woocommerce'); ?></h3>
            <form method="post" action>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><?php _e('Replace Shop page', 'typesense-search-for-woocommerce'); ?></th>
                        <td>
                            <fieldset>
                                <label for="replace_shop">
                                    <input id="replace_shop" <?php checked($replace_shop, true); ?>
                                           name="replace_shop" value="true" type="checkbox"/>

                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Filters', 'typesense-search-for-woocommerce'); ?></th>
                        <td>
                            <fieldset>
                                <label for="show_cat">
                                    <input id="show_cat" <?php checked($show_cat, true); ?> name="show_cat"
                                           value="true" type="checkbox"/>
                                    <?php _e('Enable category', 'typesense-search-for-woocommerce'); ?>
                                </label></br>

                                <label for="show_price">
                                    <input id="show_price" <?php checked($show_price, true); ?> name="show_price"
                                           value="true" type="checkbox"/>
                                    <?php _e('Enable price', 'typesense-search-for-woocommerce'); ?>
                                </label></br>

                                <label for="show_rating">
                                    <input id="show_rating" <?php checked($show_rating, true); ?> name="show_rating"
                                           value="true" type="checkbox"/>
                                    <?php _e('Enable rating', 'typesense-search-for-woocommerce'); ?>
                                </label></br>

                                <label for="show_attr">
                                    <input id="show_attr" <?php checked($show_attr, true); ?> name="show_attr"
                                           value="true" type="checkbox"/>
                                    <?php _e('Enable attributes', 'typesense-search-for-woocommerce'); ?>
                                </label></br>

                                <label for="show_sortby">
                                    <input id="show_sortby" <?php checked($show_sortby, true); ?> name="show_sortby"
                                           value="true" type="checkbox"/>
                                    <?php _e('Enable sortby', 'typesense-search-for-woocommerce'); ?>
                                </label></br>

                                <label for="default_sort_by">
                                    <?php _e('Select Default Sort By', 'typesense-search-for-woocommerce'); ?><br/>
                                    <select id="default_sort_by" name="default_sort_by">
                                        <?php
                                        foreach ($sort_by_options as $sort_by_option => $label) {
                                            ?>
                                            <option value="<?php echo $sort_by_option ?>" <?php selected($default_sort_by, $sort_by_option); ?>><?php echo $label; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </label></br>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Pagination', 'typesense-search-for-woocommerce'); ?></th>
                        <td>
                            <fieldset>
                                <label for="show_pagination">
                                    <input id="show_pagination" <?php checked($show_pagination, true); ?>
                                           name="show_pagination" value="true" type="checkbox"/>
                                    <?php _e('Enable Pagination', 'typesense-search-for-woocommerce'); ?>

                                </label>

                                <label for="pagination_type">
                                    <?php _e('Pagination Type', 'typesense-search-for-woocommerce'); ?><br/>
                                    <select id="pagination_type" name="pagination_type">
                                        <?php
                                        foreach ($pagination_type_options as $pagination_type_option => $label) {
                                            ?>
                                            <option value="<?php echo $pagination_type_option ?>" <?php selected($pagination_type, $pagination_type_option); ?>><?php echo $label; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </label></br>
                                <th scope="row"><?php _e('Show More Text', 'typesense-search-for-woocommerce'); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="show_more_text">
                                            <input id="show_more_text" name="show_more_text"
                                                   value="<?php echo esc_attr($show_more_text); ?>" type="text"/>
                                        </label>
                                    </fieldset>
                                </td>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Show featured products first', 'typesense-search-for-woocommerce'); ?></th>
                        <td>
                            <fieldset>
                                <label for="show_featured_first">
                                    <input id="show_featured_first" <?php checked($show_featured_first, true); ?>
                                           name="show_featured_first" value="true" type="checkbox"/>
                                    <?php _e('Display featured products first while filtering', 'typesense-search-for-woocommerce'); ?>

                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Search placeholder', 'typesense-search-for-woocommerce'); ?></th>
                        <td>
                            <fieldset>
                                <label for="search_placeholder">
                                    <input id="search_placeholder" name="search_placeholder"
                                           value="<?php echo esc_attr($search_placeholder); ?>" type="text"/>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Replace Product Search', 'typesense-search-for-woocommerce'); ?></th>
                        <td>
                            <fieldset>
                                <label for="replace_product_search">
                                    <input id="replace_product_search" <?php checked($replace_product_search == 'autocomplete'); ?>
                                           name="replace_product_search" value="autocomplete" type="radio"/>
                                    <?php _e('With autocomplete search.', 'typesense-search-for-woocommerce'); ?>
                                </label>
                            </fieldset>
                            <fieldset>
                                <label for="replace_product_search_with_popup">
                                    <input id="replace_product_search_with_popup" <?php checked($replace_product_search == 'popup'); ?>
                                           name="replace_product_search" value="popup" type="radio"/>
                                    <?php _e('With popup.', 'typesense-search-for-woocommerce'); ?>
                                    <p class="description">Please add class "wc-block-product-search__fields" to your
                                        search widget for WooCommerce Popup Search to run</p>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Autocomplete Submit Action', 'typesense-search-for-woocommerce'); ?></th>
                        <td>
                            <fieldset>
                                <label for="autocomplete_submit_action_keep_panel_open">
                                    <input id="autocomplete_submit_action_keep_panel_open" <?php checked($autocomplete_submit_action == 'keep_open'); ?>
                                           name="autocomplete_submit_action" value="keep_open" type="radio"/>
                                    <?php _e('Keep Autocomplete Panel Open', 'typesense-search-for-woocommerce'); ?>
                                </label>
                            </fieldset>
                            <fieldset>
                                <label for="autocomplete_submit_action_default_product_search">
                                    <input id="autocomplete_submit_action_default_product_search" <?php checked($autocomplete_submit_action == 'default_product_search'); ?>
                                           name="autocomplete_submit_action" value="default_product_search"
                                           type="radio"/>
                                    <?php _e('Use Default product search', 'typesense-search-for-woocommerce'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <h3><?php _e('Advanced Settings', 'typesense-search-for-woocommerce'); ?></h3>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><?php _e('Enable hierarchical menu', 'typesense-search-for-woocommerce'); ?></th>
                        <td>
                            <fieldset>
                                <label for="make_category_hierarchical_menu">
                                    <input id="make_category_hierarchical_menu" <?php checked($make_category_hierarchical_menu, true); ?>
                                           name="make_category_hierarchical_menu" value="true" type="checkbox"/>
                                    <?php _e('Make the default Category filter hierarchical menu.', 'typesense-search-for-woocommerce'); ?>
                                </label>
                            </fieldset>
                            <i style="color: maroon;"><?php _e('Please note that enabling it will be resource heavy for your site. Also products should be re-indexed too.', 'typesense-search-for-woocommerce'); ?></i>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button button-primary"
                           value="<?php _e('Save Changes', 'typesense-search-for-woocommerce'); ?>">
                </p>
                <?php wp_nonce_field('verify_cm_typesense_woo_nonce', 'cm_typesense_woo_nonce'); ?>
            </form>
        </div>
    <?php }
}
