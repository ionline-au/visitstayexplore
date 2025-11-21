<?php
/**
 * My Account my listings
 *
 * Shows the first intro screen on the account my-listings.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-listings.php.
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

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// get all the posts by this user
$args2 = array(
	'post_type' => 'listings',
	'post_author' => array(get_current_user_id()),
	'posts_per_page' => -1,
	'post_status' => 'any'
);
$listings = get_posts($args2);
foreach ($listings as $k => $listing) { // for some reason, it is just grabbing all the posts
	if ($listing->post_author != get_current_user_id()) {
		unset($listings[$k]);
	}
}

/**
 * in the event that a subscription is made but there is no listing associated with it
 */
// get all orders by this user
$args = array(
	'limit' => -1,
	'post_status' => array('completed'),
	'orderby' => 'date',
	'order' => 'DESC',
);
$customer_orders = [];
$customer_orders = wc_get_orders($args);
if (!empty($customer_orders)) {
	foreach ($customer_orders as $c_order) {
		if ($c_order->get_user_id() == get_current_user_id()) {
			$current_orders[] = wc_get_order($c_order->get_id());
		}
	}
}

// now loop through these orders and if there is a subscription id, this means the listing has been placed and so should not be shown here
foreach ($current_orders as $key => $cu_order) {
	/*
		update_post_meta( sanitize_text_field($cu_order->get_id() ), 'some_meta', sanitize_text_field( 'xyz' ) );
		$meta = get_post_meta($cu_order->get_id());
		dd($cu_order->get_id());
		dd($meta); exit();
	*/
	if (!empty(get_post_meta($cu_order->get_id(), 'listing_id', true))) {
		// unset($current_orders[$key]); // unset for testing
	}
}
?>
<br>
<h1 style="font-size: 2rem;padding-bottom:10px;">Adding A New <span style="color:#052698;"> Listing</span></h1>
<p>Want to get a Business Listing on Visit Stay Explore? Don't worry! Its super quick and easy! <a href="/subscriptions/" style="font-weight: bold;">Click here</a> and under the section titled <i>"Visit Stay Explore Subscriptions"</i> - then select the most appropriate plan for your business.</p>

<?php
$current_orders = '';
// found empty subscriptions
if (!empty($current_orders)) {
	echo '<h1 style="font-size: 2rem;padding-bottom:10px;">Subscriptions With <span style="color:#052698;">No Listings</span></h1>';
	echo '<p>Looks like you have valid subscription(s), but no business listings associated with them yet. Please create a listing for each of your subscriptions.</p>';
	?>
    <div class="business-listings">
        <table class="table">
            <thead>
            <tr>
                <th width="33%">Order/Subscription ID</th>
                <th width="33%">Purchase Date</th>
                <th width="33%"></th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($current_orders as $un_listing) { $meta = get_post_meta($un_listing->get_id()); ?>
                <tr>
                    <td>
                        <p style="text-align: center;">#<?php echo $un_listing->get_id(); ?></p>
                    </td>
                    <td>
                        <p style="text-align: center;"><?php echo date('d/m/Y H:i', strtotime($meta['_completed_date'][0])); ?></p>
                    </td>
                    <td>
                        <p style="text-align: center;"><a href="/add-business/?subscription_id=<?php echo $un_listing->get_id(); ?>" style="background-color: #FF8210;color:white !important;padding:8px;padding-left:15px;padding-right:15px;">ADD BUSINESS LISTING + </a></p>
                    </td>
                </tr>
			<?php } ?>
            </tbody>
        </table>
    </div>
	<?php
}

if (!empty($listings)) {
	?>
    <h1 style="font-size: 2rem;padding-bottom:10px;">Existing <span style="color:#052698;">Listings</span></h1>
    <p>Below is a list of business listings with active subscriptions, you can manage your listings here.</p>
    <div style="text-align: center;background-color: #bff6bf;color:black;border-radius: 5px;margin-bottom:10px;margin-top:10px;padding:10px;">Your listings will not be available until they are 'Published'. Edit your listing(s) to publish them.</div>
    <div class="business-listings hide-for-mobile">
        <table class="table">
            <thead>
            <tr>
                <th width="350">Title</th>
                <th width="">Status</th>
                <!--<th width="">Subscription Type / ID</th>-->
                <th width="">QR Code</th>
                <th width="">Listing</th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($listings as $listing) { ?>
                <tr>
                    <td>
                        <p style="text-align: center;font-size:13px;margin-top:10px;"><?php echo $listing->post_title; ?></p>
                    </td>
                    <td>
                        <p style="text-align: center;margin-top:10px;margin-bottom:10px;">
                            <?php
                                if ($listing->post_status == 'publish') {
                                    echo 'Published';

                                } else {
                                    echo 'Unpublished';
                                }
                            ?>
                        </p>
                    </td>
                    <!--
                    <td>
                        <p style="text-align: center;">
							<?php
							global $wpdb;
							$all_meta = get_post_meta($listing->ID);
							$subscription_id = $all_meta['subscription_id'][0];

							if (isset($subscription_id)) {
								$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id ."'";
								$order_details = $wpdb->get_results($sql);

								if (!empty($order_details)) {
									$order = wc_get_order( $order_details[0]->order_id );
									$items = $order->get_items();
									$product = '';
									foreach ( $items as $item ) {
										$product = $item->get_product();
									}
									echo $product->get_name() . ' / #' . $order_details[0]->order_id;
								}
							} else {
								echo 'No Subscription Found';
							}
							?>
                        </p>
                    </td>
                    -->
                    <td>
	                    <?php
                            if ($listing->post_status == 'publish') {
                                ?>
                                <p style="text-align: center;margin-top:10px;margin-bottom:10px;"><a href="/product/promotional-business-decal/" style="background-color: #FF8210;color:white !important;padding:8px;padding-left:15px;padding-right:15px;">BUY DECALS</a></p>
                                <?php
                            }
	                    ?>
                    </td>
                    <td>
                        <p style="text-align: center;margin-top:10px;margin-bottom:10px;"><a href="<?php echo site_url() ?>/edit-business/?id=<?php echo $listing->ID; ?>" style="background-color: #FF8210;color:white !important;padding:8px;padding-left:15px;padding-right:15px;">EDIT</a></p>
                    </td>
                </tr>
			<?php } ?>
            </tbody>
        </table>
    </div>

    <div class="hide-for-desktop">
		<?php foreach ($listings as $listing) { ?>
            <div style="border:1px solid #ccc;padding:15px;margin:5px;margin-bottom:15px;">
                <p style="text-align: center;font-weight: bold;padding:0;margin:0;color:black;font-size: 15px;"><?php echo $listing->post_title; ?></p>
                <p style="text-align: center;font-weight: normal;padding:0;margin:0;color:black;font-size: 12px;margin-bottom:10px;">Created: <?php echo date('d/m/Y', strtotime($listing->post_modified)); ?></p>
                <p style="text-align: center;"><a href="<?php echo site_url() ?>/edit-business/?id=<?php echo $listing->ID; ?>" style="background-color: #FF8210;color:white !important;padding:8px;padding-left:15px;padding-right:15px;">EDIT BUSINESS LISTING</a></p>
                <div style="margin-bottom:10px;"></div>
            </div>
		<?php } ?>
    </div>
	<?php
}
?>
