<?php
/*
 * tmpl-cm-tsfwc-shortcode-[post-type-slug]-search-results
 * for different templates for different post types add the post type slug instead of [post-type-slug] as the id
 * example tmpl-cm-tsfwc-shortcode-page-search-results or tmpl-cm-tsfwc-shortcode-book-search-results
 */

?>
<script type="text/html" id="tmpl-cmtsfwc-HitsTemplate">
    {{{data.sale_flash}}}
    <a href="{{{data._highlightResult.permalink.value}}}" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
        <# if(data.product_thumbnail_html !== undefined && data.product_thumbnail_html.length > 0) { #>
            {{{data.product_thumbnail_html}}}
        <# } else { #>
            <# if(data.post_thumbnail !== undefined && data.post_thumbnail !== '' && data.post_thumbnail.length > 0) { #>
            <img src="{{{data.post_thumbnail}}}"/>
            <# } else { #>
            <img src="<?php echo plugins_url( 'assets/placeholder.jpg', CODEMANAS_TYPESENSE_FILE_PATH ) ?>"/>
            <# } #>
        <# } #>
        <h2 class="woocommerce-loop-product__title">{{{data.formatted.post_title}}}</h2>
        <div class="hit-rating">{{{data.rating_html}}}</div>
        <div class="hit-price">{{{data.price_html}}}</div>

        <div x-data="{productId:{{{data.id}}}}">
            <?php wc_get_template('ui/apl-wishlist-button.php'); ?>
        </div>

    </a>
    <!-- <div class="hit-cats">{{{data.cat_links_html}}}</div> -->
    {{{data.add_to_cart_btn}}}
</script>
