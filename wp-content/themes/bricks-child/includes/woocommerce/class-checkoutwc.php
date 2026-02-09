<?php
/**
 * Checkoutwc class
 * 
 * Compability with plugin checkoutwc
 */
class Checkoutwc {
    function __construct() {
        add_filter('cfw_address_autocomplete_shipping_countries', [$this, 'address_autocomplete_countries']);
    }

    function address_autocomplete_countries() {
        return ['au'];
    }
}