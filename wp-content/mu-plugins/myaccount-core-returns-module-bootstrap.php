<?php
/**
 * Plugin Name: MyAccount Core Returns Module Bootstrap
 * Description: Enables the MyAccount Core returns/exchanges module for this site.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'myaccount_core_returns_module_enabled', '__return_true' );
