<?php

class Admin_Manager {
    private $script_loader;
    private $other_admin_class;

    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    private function load_dependencies() {
        require_once CHILD_DIR . '/includes/admin/class-admin-scripts.php';

        $this->script_loader = new Admin_Script_Loader();



        // Load other dependencies
    }

    private function define_admin_hooks() {
        // Định nghĩa các hook và filter cho các class admin
    }
}

new Admin_Manager();