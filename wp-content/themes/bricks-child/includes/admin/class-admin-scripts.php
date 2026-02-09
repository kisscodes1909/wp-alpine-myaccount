<?php

class Admin_Script_Loader {
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function enqueue_admin_scripts() {
        $screen = get_current_screen();

        if ( $screen->id === 'shop_order' ) {
            $script_path = get_stylesheet_directory() . '/assets/js/admin-approve-return.js';
            $file_modi = filemtime($script_path);
            wp_enqueue_script('approve-return', CHILD_URL . '/assets/js/admin-approve-return.js', ['acf-input'], $file_modi, true);
        }
    }
}

