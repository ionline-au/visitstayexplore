<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$allowed_html = array(
    'a' => array(
        'href' => array(),
    ),
);
?>

    <h1 style="font-size: 2rem;padding-bottom:10px;">Welcome To Visit Stay <span style="color:#052698;">Explore</span></h1>
    <p>
        <?php
        printf(
        /* translators: 1: user display name 2: logout url */
            wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ), $allowed_html ),
            '<strong>' . esc_html( $current_user->display_name ) . '</strong>',
            esc_url( wc_logout_url() )
        );
        ?>
    </p>
    <p><strong>Welcome to the Visit Stay <span style="color:#052698;">Explore</span> Dashboard!</strong> You can manage your business listings, manage your profile's information and much more!</p>

    <h1 style="font-size: 2rem;padding-bottom:10px;">Selecting The Right Subscription For <span style="color:#052698;">Your Business</span></h1>
    <p>To get started, you will first need to choose a 'business subscription' from our platform. We have several different subscriptions available depending on your requirements. Need help. <a href="/contact-us">Contact Us</a></p>
    <div>
        <div style="float: left;margin-right:15px;">
            <p style="text-align: center;"><a href="/subscriptions/" style="background-color: #FF8210;color:white !important;padding:8px;padding-left:15px;padding-right:15px;">CHOOSE A SUBSCRIPTION</a></p>
        </div>
        <div style="float: left;">
            <p style="text-align: center;"><a href="/my-account/my-listings/" style="background-color: #FF8210;color:white !important;padding:8px;padding-left:15px;padding-right:15px;">VIEW EXISTING LISTINGS</a></p>
        </div>
        <div style="clear: both;"></div>
    </div>
    <br>

    <h1 style="font-size: 2rem;padding-bottom:10px;margin-top:20px;">Leave <span style="color:#052698;">Recommendations</span></h1>
    <p>It's great to hear about previous customer experiences before engaging a business, if you have used the services of one of the Visit Stay Explore businesses listed, please make sure you leave them a recommendation on their Profile Page, thank you!</p>

    <h1 style="font-size: 2rem;padding-bottom:10px;">Getting <span style="color:#052698;">Help</span></h1>
    <p>Do you have a question or need a hand deciding what subscription is right for your business? Not a problem, our friendly and dedicated support team is on hand to help you. You can send us an email at <a href="mailto:contact@visitstayexplore.staging-sites.com.au">contact@visitstayexplore.staging-sites.com.au</a> or <a href="/contact-us/">click here</a> to send us a message</p>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_before_my_account' );

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
