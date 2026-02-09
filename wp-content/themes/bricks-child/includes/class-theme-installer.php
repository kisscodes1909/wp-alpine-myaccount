<?php

class Theme_Installer extends Singleton {
    protected function __construct()
    {
        add_action( 'after_setup_theme', [$this, 'image_sizes'] );
    }

    function image_sizes() {
        add_image_size( 'order-product-item', 150);
    }
}

return Theme_Installer::getInstance();
