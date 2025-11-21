<?php
/**
 * Plugin Name: Visit Stay Explore - Custom Property Listing
 * Plugin URI: https://ionline.com.au/
 * Description: Render listings from the Visit Stay Explore API.
 * Version: 1
 * Author: Matthew Johnson
 * Author URI: https://ionline.com.au/
 **/

/**
 * Enqueue the css for the Listings
 */
add_action('wp_head', function () {
    wp_enqueue_style('listing-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css');

});
/**
 * Property Listing Shortcode
 */
add_shortcode('property-listings', 'propertyListings');
function propertyListings()
{
    ob_start();
    require_once(plugin_dir_path(__FILE__) . '/template/listings.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

/**
 * Home page region
 */
add_shortcode('home-page-region', 'homePageRegion');
function homePageRegion()
{
    ob_start();
    require_once(plugin_dir_path(__FILE__) . '/template/home-page-region.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

/**
 * Home page Filter
 */
add_shortcode('home-page-filter', 'homePageFilter');
function homePageFilter()
{
    ob_start();
    require_once(plugin_dir_path(__FILE__) . '/template/home-page-filter.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

/**
 * Subscription fetch
 */
add_shortcode('home-page-subscriptions', 'homePageSubscriptions');
function homePageSubscriptions()
{
    ob_start();
    require_once(plugin_dir_path(__FILE__) . '/template/home-page-subscriptions.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}


/**
 * Dashboard subscriptions
 */
add_shortcode('dashboard-subscriptions', 'DashboardSubscriptions');
function DashboardSubscriptions()
{
    ob_start();
    require_once(plugin_dir_path(__FILE__) . '/template/dashboard-subscriptions.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}