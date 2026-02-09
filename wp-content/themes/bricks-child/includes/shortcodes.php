<?php

// Add Shortcode
function newsletter_form_func() {
    ob_start();
    ?>
        <div class="w-full">
            <h3 class="widget-title">Stay in touch</h3>
            <div class="flex flex-col items-center">
                <input class="bg-gray-100 rounded-lg rounded-r-none text-base leading-none p-5 w-4/5" type="email" placeholder="Your Email" />
                <!--<button class="button btn mt-3 max-w-[150px]">subscribe</button>-->
            </div>
        </div>
    <?php

	$html = ob_get_contents();
	ob_end_clean();
	return $html;	
}
add_shortcode( 'newsletter_form', 'newsletter_form_func' );