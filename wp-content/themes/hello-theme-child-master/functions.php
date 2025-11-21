<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts()
{
    wp_enqueue_style(
        'hello-elementor-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [
            'hello-elementor-theme-style',
        ],
        '1.0.0'
    );
}

add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts');

function filter_woocommerce_login_redirect($redirect, $user)
{
    $subscription_id = '';
    $users_subscriptions = wcs_get_users_subscriptions($user->ID);
    foreach ($users_subscriptions as $subscription) {
        if ($subscription->has_status(array('active'))) {
            $subscription_id = $subscription->get_id();
        }
    }
    if ($subscription_id) {
        $redirect = $redirect . '/view-subscription/' . $subscription_id;
    } else {
        $redirect = $redirect . '/subscriptions/';
    }
    return $redirect;
}

;

// add the filter 
add_filter('woocommerce_login_redirect', 'filter_woocommerce_login_redirect', 10, 2);

add_filter('woocommerce_account_menu_items', 'remove_my_account_links', 10, 1);
function remove_my_account_links($menu_links)
{
    // unset( $menu_links['dashboard'] ); // Remove Dashboard
    unset($menu_links['orders']); // Remove Orders

    $menu_links = array_slice($menu_links, 0, 1, true)
        + array('my-listings' => 'Listings')
        + array_slice($menu_links, 1, NULL, true);

    return $menu_links;
}

function my_custom_endpoints()
{
    add_rewrite_endpoint('my-listings', EP_ROOT | EP_PAGES);
}

add_action('init', 'my_custom_endpoints');

function my_custom_query_vars($vars)
{
    $vars[] = 'my-listings';

    return $vars;
}

add_filter('query_vars', 'my_custom_query_vars', 0);

function my_custom_flush_rewrite_rules()
{
    flush_rewrite_rules();
}

add_action('after_switch_theme', 'my_custom_flush_rewrite_rules');


add_action('init', 'silva_add_endpoint');
function silva_add_endpoint()
{
    // WP_Rewrite is my Achilles' heel, so please do not ask me for detailed explanation
    add_rewrite_endpoint('my-listings', EP_PAGES);

}

add_action('woocommerce_account_my-listings_endpoint', 'silva_my_account_endpoint_content');
function silva_my_account_endpoint_content()
{
    wc_get_template('myaccount/my-listings.php');

}