=== Typesense Search for WooCommerce ===
Contributors: codemanas, digamberpradhan, sachyya-sachet, j__3rk, ugene
Tags: search, typesense, lightning fast, autocomplete, instant search
Requires at least: 5.8
Tested up to: 6.0.0
Requires PHP: 7.4
Stable tag: 1.7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightning fast search for your WooCommerce site, powered by Typesense.

== Description ==

Typesense Search for WooCommerce is a premium addon for the free Search with Typesense plugin.

The addon supercharges your WooCommerce site search functionality with Typesense providing search-as-you-type experience. Product searching will be instant and super fast which enhances user experience on your site.

Moreover it provides filtering and sorting options which are faster and instantaneous than the default ones provided by WooCommerce. It acts as a bridge between your WooCommerce site and Typesense search engine.

**FEATURES**

- Lightning-fast product search results in miliseconds
- Allow to override native WooCommerce default search for whole site.
- Shortcodes for adding search in only specific locations.
- Hooks and filters for customizations
- Template Override for design customizations.
- Developer friendly
- Autocompletion for WooCommerce search widget and block

**LINKS**

[Documentation](https://codemanas.github.io/cm-typesense-docs/tsfwc/)
[Typesense](https://typesense.org/)

**DEMO LINKS**

[Demo](https://typesense.codemanas.com/woocommerce/)

== Installation ==
= Minimum Requirements =
* PHP 7.4 required or greater
* MySQL 5.6 or greater is recommended

= Automatic Installation =
Go to WordPress Plugins > Add New Search for "Search with Typesense"
Click Install and then activate Plugin

= Manual Installation =
If for some reason automatic installation is not possible, go to [https://wordpress.org/plugins/search-with-typesense](https://wordpress.org/plugins/search-with-typesense) and you will see the download button. Clicking download button will provide you with a zip file of the plugin then.

Go to WordPress Plugins > Add New and click upload plugin.
Click upload plugin and then add the zip file
The plugin will then be installed, then activate the plugin.

== Changelog ==

= 1.7.2 Apr 2,2024 =
* Fix: Add class `cmtsfwc-NextButton--disabled` when it's the last page of the result and hide the button
* Update: Update the mobile view design of instant search results widget
* Update: Remove `postedDate` from template data 

= 1.7.1 Mar 21,2024 =
* Fix: Fix the typo in instanct search JS

= 1.7.0 Mar 21,2024 =
* Fix: Add validation for `additionalConfig`

= 1.6.9 Mar 20,2024 =
* Fix: Add validation for the added filter: `cm_tsfwc_additional_config`

= 1.6.8 Mar 19,2024 =
* Enhancement: Add filter `cm_tsfwc_additional_config`
* Enhancement: Add `preserve_filter_reset_on_archive` parameter to clear preselected filter on archive pages

= 1.6.7 Feb 7,2024 =
* Enhancement: Pass `tsfwInstance` to window global instace so user can hook event

= 1.6.6 Jan 11,2024 =
* Enhancement: Add option for infinite button on shortcode and admin settings

= 1.6.5 Dec 20,2023 =
* Fix: Fix the registration and localization of autocomplete.js
* Enhancement: Add option to turn off/on of autofocus
* Enhancement: Add hooks for localized data on autocomplete and instant-search JS

= 1.6.4 Sep 28,2023 =
* Enhancement: Added option to enable/disable routing

= 1.6.3 Aug 22,2023 =
* Enhancement: Show catalog according to paginated link displayed

= 1.6.2 Jun 23,2023 =
* Fix: Fix updated WooCommerce search block which is to be replaced by Autocomplete.

= 1.6.1 Jun 14,2023 =
* Enhancement: Update admin to use new js interface for smoother UI/UX experience.

= 1.6.0 May 30,2023 =
* Dev: Code refactoring for JS for future development.

= 1.5.0 May 03,2023 =
* Feature: Make Text match desc the default way products are sorted along with any additional sorting parameters
* Dev: Change how arguments are passed for hooks `cm_tsfwc_instant_search_results_main_panel` and `cm_tsfwc_instant_search_results_header_output` gradually change will be made for all other hooks as well

= 1.4.11 April 05,2023 =
* Dev: Added filter to modify shortcode parameters

= 1.4.10 April 03, 2023 =
* Dev: Fix warning for replace_product_search

= 1.4.9 February 28, 2023 =
* Update: Add to cart HTML generation changed to use the loop HTML instead. Indexing needed for this update to work.

= 1.4.8 February 22, 2023 =
* Feature: Add filter `cm_tsfwc_search_query_length` to search only after certain character length

= 1.4.7 February 17, 2023 =
* Fix: Updated Instant Search JS slight design issue for refinement list

= 1.4.6 February 09, 2023 =
* Fix: Minified script was generating error.

= 1.4.5 November 15, 2022 =
* Dev Fix: Popup not closing, added instructions for popup

= 1.4.4 September 22, 2022 =
* Feature: Add shortcode for autocomplete

= 1.4.3 September 20, 2022 =
* Feature: Added ability to either continue with default WooCommerce Product Search or Keep Search open
* Fix: Fixed popup on classic widget search form

= 1.4.2 August 30, 2022 =
* Feature: Added InstantSearch Popup option to replace default WooCommerce Search

= 1.4.1 August 26, 2022 =
* Hotfix: Multiple Attributes not showing

= 1.4.0 August 18, 2022 =
* (Major Update: See Blog post for more details)[https://www.codemanas.com/]
* Enhancement : HTML Structure changed to be more simplified and maintainable - following SUITS css naming convention
* Enhancement : Mobile Device redesign for

= 1.3.9 August 15, 2022 =
* Enhancement: Added Major update notification on plugin update page

= 1.3.8 July 20, 2022 =
* Enhancement: Chunk single index.js file into different chunks: autocomplete and instant-search; and only load them when required

= 1.3.7 July 13, 2022 =
* Enhancement: Add filter `cm_tsfwc_additional_autocomplete_params` to add additional parameters for autocomplete

= 1.3.6 July 12, 2022 =
* Enhancement: Add filter `cm_tsfwc_additional_search_params` to add additional parameters

= 1.3.5 July 11, 2022 =
* Enhancement: Add option to change category menu to hierarchial menu

= 1.3.4 May 23, 2022 =
* Dev Enhancement: Made plugin ready for multiple-site same cluster configuration

= 1.3.3 May 19, 2022 =
* Minor Fix: Update text domain for product attribute label

= 1.3.2 May 20, 2022 =
* WPML Fix: Sprintf incorrectly used.

= 1.3.1 May 18,2022 =
* Enhancement: Index products when stock is reduced

= 1.3.0 Apr 24, 2022 =
* Dev Enhancement: Switch to action hooks for templating for granular control
* Dev Enhancement: Javascript validation for missing facets / widgets

= 1.2.9 Apr 15, 2022 =
* Enhancement: WPML made query label translatable

= 1.2.8 Apr 13, 2022 =
* Dev Enhancement: Analytics Middleware added, so 3rd party developers can retrieve current search state and send to 3rd party analytics

= 1.2.7 Apr 12, 2022 =
* WPML Support: Make currentRefinements and clearRefinements translatable"

= 1.2.6 Apr 11, 2022 =
* Dev Enhancement: add cm_tsfwc_locate_template filter to allow 3rd party plugin and page builders to allow editing templates

= 1.2.5 Apr 4, 2022 =
* Enhancement: Add filters to change facet settings and facet types
* Enhancement: Make it compatible with free update by changing filter `cm_typesense_available_post_types` to `cm_typesense_available_index_types`

= 1.2.4 March 31, 2022 =
* Enhancement: WPML support for product attributes and custom taxonomy

= 1.2.3 March 28, 2022 =
* Enhancement: WPML Support for Bulk Index, front end string localization

= 1.2.2 March 24, 2022 =
* Fix: Pagination not working when WPML is added

= 1.2.1 March 22, 2022 =
* Fix: Default sort by not selected

= 1.2.0 March 22, 2022 =
* Enhancement : WPML Compatibility added

= 1.1.2 March 18, 2022 =
* Fix: Sort By not working
* Enhancement: Let users choose default sort by
* Make strings translatable

= 1.1.1 February 16, 2022 =
* Theme compat changes for themes that modify
= 1.1.0 February 11, 2022 =
* Dev: Update styling to be compatible with version 1.2.3 of core plugin

= 1.0.1 February 9, 2022 =
* Change "hijack" word to "replace"

= 1.0.0 February 2, 2022 =
* Initial Release
