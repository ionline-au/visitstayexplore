<?php
/**
 * Plugin Name: Visit Stay Explore - Customisations Plugin
 * Plugin URI: https://ionline.com.au/
 * Description: Customisations plugin for Visit Stay Explore for management of bespoke content.
 * Version: 1
 * Author: Matthew Johnson
 * Author URI: https://ionline.com.au/
 **/

// simple debugging function
if (!function_exists('dd')) {
	function dd($variable)
	{
		echo '<pre>';
		if ($variable != '') {
			print_r($variable);
		}
		echo '<br>';
		echo '</pre>';
	}
}

/**
 * Enqueue the css for the Listings
 */
add_action('wp_head', function () {
	wp_enqueue_style('sitewide-styles', plugin_dir_url(__FILE__) . 'css/sitewide.css?'.uniqid());
	if (is_page('my-account')) {
		wp_enqueue_style('my-account-styles', plugin_dir_url(__FILE__) . 'css/my-account.css?'.uniqid());
	}
});

add_shortcode('ionline_get_business_listing_header_info', function ($atts) {

	global $wpdb;

	if (!isset($atts['post'])) {
		return false;
	}
	if (!is_numeric($atts['post'])) {
		return false;
	}

	// get everything we need for rendering
	$listing = get_post($atts['post']);
	$all_meta = get_post_meta($listing->ID);

	// get the subscription and order details
	$subscription_id = $all_meta['subscription_id'][0];
	$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id . "'";
	$order_details = $wpdb->get_results($sql);

	// get the product details from the order, then query the meta for that product
	$order = wc_get_order($order_details[0]->order_id);
	$items = $order->get_items();
	foreach ($items as $item) {
		$product = $item->get_product();
	}
	$town_limit = get_post_meta($product->get_id(), 'town_limit', true);

	// render it
	$html = '<h5 style="text-align: center;font-weight: bold;font-weight: bold;font-size: 20px;padding:0;margin:0;">' . $product->get_name() . ' - Subscription #' . $order_details[0]->order_id . '</h5>';
	$html .= '<p style="font-size:15px;font-weight: normal;text-align:center;padding:0;margin:0;">You have selected <span id="dynamic_selected">0</span> / <span id="max_limit">' . $town_limit . '</span> areas</p><br>';
	// $html .= '<p style="font-size:15px;font-weight: normal;text-align:center;padding:0;margin:0;">Max <span id="max_limit">' . $town_limit . '</span> areas</p><br>';

    if ($_GET['message'] == 'limit_hit') {
	    $html .= '<p style="margin-top:-10px;font-size:15px;font-weight: normal;text-align:center;padding:10px;margin:0;margin-bottom:20px;background-color:green;color:white;border-radius:10px">You have selected more areas than your plan allows.</p>';
    }

	return $html;


});

// hide banner on listings page
add_action('wp_head', function () {
	global $post;

	if (!isset($post)) {
		return false;
	}

    // /events/map/
    if ($_SERVER['REQUEST_URI'] == '/events/map/') {
        echo '<style>.elementor-kit-5 button, .elementor-kit-5 input[type="button"], .elementor-kit-5 input[type="submit"], .elementor-kit-5 .elementor-button { background-color: white !important; }</style>';
    }

	// sitewide
	?>
    <style>
        .elementor-kit-5 button, .elementor-kit-5 input[type="button"], .elementor-kit-5 input[type="submit"], .elementor-kit-5 .elementor-button {
            /*background-color: white !important;*/
        }
        .wcs-switch-link {
            color: white !important;
        }
        .select2-container--default .select2-results>.select2-results__options{
            max-height: 500px !important;
        }
        body > div.container > div > div.row > div > h5 > a:hover {
            color: #FF8210 !important;
        }
        .comment-edit-link {
            display: none;
        }

        div#adv-custom-pager img {
            max-width: 81px !important;
        }

        .backend_pad_left {
            padding-right: .3rem !important;
            /*margin-right: .2rem !important;*/
        }

        .backend_pad_right {
            /*margin-left: .2rem !important;*/
            padding-left: .3rem !important;
        }

        .reply {
            display: none !important;
        }

        .elementor-127 .elementor-element.elementor-element-9b0729e .elementor-nav-menu--main .elementor-item.elementor-item-active {
            color: white;
        }

        .woocommerce-message {
            background-color: #FF8210 !important;
            border-top-color: #FF8210 !important;
            color: white !important;
        }

        .woocommerce-message::before {
            content: "\e015";
            color: white !important;
        }

        .product-thumbnail {
            display: none;
        }

        .login-image {
            padding-top: 80px !important;
            width: 75% !important;
        }

        .acf-taxonomy-field .categorychecklist-holder {
            max-height: 650px !important;
        }
    </style>
	<?php
	// only render on the edit listings page - jquery for the edit listing page
	if ($post->ID == '1316') {
		?>
        <script>

            jQuery(document).ready(function () {

                // keep select2 open and dont jump arond on the additional areas
                jQuery('#acf-field_61e62e2544b10-field_62722e543d27f').select2({
                    closeOnSelect: false
                })
                .on('select2:selecting', e => jQuery(e.currentTarget).data('scrolltop', jQuery('.select2-results__options').scrollTop()))
                .on('select2:select', e => jQuery('.select2-results__options').scrollTop(jQuery(e.currentTarget).data('scrolltop')));
                jQuery('#acf-field_61e62e2544b10-field_627230328f3b9').select2({
                    closeOnSelect: false
                })
                .on('select2:selecting', e => jQuery(e.currentTarget).data('scrolltop', jQuery('.select2-results__options').scrollTop()))
                .on('select2:select', e => jQuery('.select2-results__options').scrollTop(jQuery(e.currentTarget).data('scrolltop')));

                // hide the service area on load
                jQuery('#area_selection_lockyer').hide(); // id 26
                jQuery('#area_selection_toowoomba').hide(); // id 24
                jQuery('#area_selection_south_burnett').hide(); // id 351
                jQuery('#area_selection_somerset').hide(); // id 355
                jQuery('#area_selection_western_downs').hide(); // id 365
                jQuery('#area_selection_southern_downs').hide(); // id 387
                jQuery('#area_selection_goondiwindi').hide(); // id 390

                // check to see which regions are selected and display it on load - regions select box #acf-field_61e62e2544b10-field_62722bc3d911e
                jQuery('#acf-field_61e62e2544b10-field_62722bc3d911e').each( function () {
                    var array = jQuery(this).val();
                    array.forEach(function (e) {
                        if (e == '24') {
                            jQuery('#area_selection_toowoomba').show();
                        }
                        if (e == '26') {
                            jQuery('#area_selection_lockyer').show();
                        }
                        if (e == '351') {
							jQuery('#area_selection_south_burnett').show();
						}
                        if (e == '355') {
                            jQuery('#area_selection_somerset').show();
                        }
                        if (e == '365') {
                            jQuery('#area_selection_western_downs').show();
                        }
                    });
                });

                // remove the selected regions areas
                jQuery('#acf-field_61e62e2544b10-field_62722bc3d911e').on('select2:unselect', function (e) {
                    // console.log(e.params.data);
                    if (e.params.data.id == '24') {
                        jQuery('#area_selection_toowoomba').hide();
                    }
                    if (e.params.data.id == '26') {
                        jQuery('#area_selection_lockyer').hide();
                    }
                    if (e.params.data.id == '351') {
                        jQuery('#area_selection_south_burnett').hide();
                    }
                    if (e.params.data.id == '355') {
						jQuery('#area_selection_somerset').hide();
					}
                    if (e.params.data.id == '365') {
						jQuery('#area_selection_western_downs').hide();
					}
                    if (e.params.data.id == '387') {
                        jQuery('#area_selection_southern_downs').hide();
                    }
                    if (e.params.data.id == '390') {
                        jQuery('#area_selection_goondiwindi').hide();
                    }
                });

                // add the selected regions areas
                jQuery('#acf-field_61e62e2544b10-field_62722bc3d911e').on('select2:select', function (e) {
                    // console.log(e.params.data);
                    if (e.params.data.id == '24') {
                        jQuery('#area_selection_toowoomba').show();
                    }
                    if (e.params.data.id == '26') {
                        jQuery('#area_selection_lockyer').show();
                    }
                    if (e.params.data.id == '351') {
						jQuery('#area_selection_south_burnett').show();
					}
                    if (e.params.data.id == '355') {
                        jQuery('#area_selection_somerset').show();
                    }
                    if (e.params.data.id == '365') {
                        jQuery('#area_selection_western_downs').show();
                    }
                    if (e.params.data.id == '387') {
                        jQuery('#area_selection_southern_downs').show();
                    }
                    if (e.params.data.id == '390') {
                        jQuery('#area_selection_goondiwindi').show();
                    }
                });

                // set default of 0
                sessionStorage.setItem('existing_count', 0);
                function do_something() {
                    jQuery('#area_selection_lockyer li > span.acf-selection, #area_selection_toowoomba li > span.acf-selection').each(function () { // need to select the regions
                        sessionStorage.setItem('existing_count', Number(sessionStorage.getItem('existing_count')) + 1);
                    });
                    jQuery('#dynamic_selected').html(sessionStorage.getItem('existing_count'));


                    // test to see if the max has been hit and display a message
                    if (jQuery('#max_limit').html() <= sessionStorage.getItem('existing_count')) {
                        jQuery('#max_limit_reached').fadeIn();
                    }
                }

                // run the function 3s after fully loading
                setTimeout(do_something, 3000);

            });
        </script>
		<?php
	}

	// listings archive
	if ($post->ID == 917) {
		?>
        <style>
            body > div.elementor.elementor-885.elementor-location-single.post-917.page.type-page.status-publish.hentry > section.elementor-section.elementor-top-section.elementor-element.elementor-element-71514bb.elementor-section-height-min-height.elementor-section-boxed.elementor-section-height-default.elementor-section-items-middle.jet-parallax-section {
                display: none;
            }
        </style>
		<?php
	}

	// my account
	if ($post->ID == 14) {
		?>
        <style>
            #login-form > div > a {
                padding-top: 25px !important;
            }

            .subsciption-block ul li {
                text-align: center;
                border-bottom: 1px solid #ddd;
                padding: 5px 38px;
                font-size: 15px;
                line-height: 23px;
                background: #fff;
            }

            .subsciption-block .header {
                padding: 5px;
            }

            .elementor-widget-woocommerce-my-account .woocommerce-MyAccount-content h2:first-of-type {
                margin-top: 30px;
                padding: 0;
                margin: 13px;
            }

            .subsciption-block.standard .elementor-price-table__price {
                padding: 10px;
            }

            .subsciption-block {
                margin: 0;
                padding: 0px 0px 0px 0px;
                box-shadow: 0px 0px 10px 0px #dddddd;
            }

            body > div.elementor.elementor-14 > div > section > div.elementor-container.elementor-column-gap-default > div > div > div > div > div > div > div > div > h2 {
                margin-left: 0;
                padding-left: 0;
            }

            body > div.elementor.elementor-14 > div > section > div.elementor-container.elementor-column-gap-default > div > div > div > div > div > div > div > div > section > h2 {
                margin-left: 0;
                padding-left: 0;
            }

            .elementor-widget-woocommerce-my-account .woocommerce-MyAccount-content h2:first-of-type {
                margin-top: 10px !important;
            }

            @media (min-width: 1200px) {
                .hide-for-desktop {
                    display: none;
                }
            }

            @media (min-width: 768px) and (max-width: 1024px) {
                .hide-for-desktop {
                    display: none;
                }
            }

            @media (max-width: 640px) {
                .hide-for-mobile {
                    display: none;
                }
            }

            @media (max-width: 480px) {
                .hide-for-mobile {
                    display: none;
                }
            }
        </style>
		<?php
	}

	// checkout
	if ($post->ID == 13) {
		?>
        <style>
            .woocommerce-billing-fields h3, .woocommerce-additional-fields h3, #order_review_heading {
                border-bottom: none !important;
            }

            .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover {
                background-color: #FF8210 !important;
            }

            .first-payment-date {
                display: block;
            }

            .woocommerce {
                max-width: 1120px !important;
                margin: 30px auto !important;;
            }

            h2 {
                font-size: 25px !important;
            }
        </style>
        <script>
            // replaces the word 'renewal' with 'payment' on the checkout screen every 250ms
            jQuery(document).ready(function () {
                setInterval(do_replace, 250);
            });

            function do_replace() {
                var html;
                html = jQuery('#order_review > table > tfoot > tr.order-total.recurring-total > td > div').html();
                html = html.replace('renewal', 'payment');
                jQuery('#order_review > table > tfoot > tr.order-total.recurring-total > td > div').empty().html(html);
            };
        </script>
		<?php
	}

	// add business listing
	if ($post->ID == 1104 || $post->ID == 1316) {
		?>
        <style>
            #form_61e631afb4115 > div > div.table-listing-order.af-field.af-field-type-group.af-field-listing-information-group.acf-field.acf-field-group.acf-field-61e62e2544b10 > div.af-label.acf-label > label,
            #form_61e631afb4115 > div > div.af-field.af-field-type-group.af-field-contact-group.acf-field.acf-field-group.acf-field-61e62e3c44b11 > div.af-label.acf-label > label,
            #form_61e631afb4115 > div > div.table-services.af-field.af-field-type-group.af-field-services-group.acf-field.acf-field-group.acf-field-61e62ea3513d6 > div.af-label.acf-label > label {
                font-size: 2rem !important;
                padding-bottom: 5px !important;
                font-family: "Work Sans", sans-serif !important;
                color: black !important;
                font-weight: bold !important;
            }

            #form_61e631afb4115 > div > div.af-field.af-field-type-group.af-field-contact-group.acf-field.acf-field-group.acf-field-61e62e3c44b11 > div.af-label.acf-label > label {
                margin-top: 20px !important;
            }

            .elementor-kit-5 button, .elementor-kit-5 input[type="button"], .elementor-kit-5 input[type="submit"], .elementor-kit-5 .elementor-button {
                background-color: transparent !important;
            }

            .elementor-kit-5 button:hover, .elementor-kit-5 input[type="button"]:hover, .elementor-kit-5 input[type="submit"]:hover, .elementor-kit-5 .elementor-button:hover {
                background-color: transparent !important;
            }

            .acf-form .acf-form-submit button.acf-button.af-submit-button {
                border-radius: 0px;
                border: 1px solid #ff8210;
                background: #ff8210 !important;
                color: #fff;
                font-family: "Work Sans", Sans-serif;
            }

            h2.media-attachments-filter-heading {
                display: none;
            }

            #media-attachment-filters {
                display: none;
            }

            #menu-item-browse {
                display: none;
            }

            .media-menu-item {
                color: black;
            }

            a.acf-button.button {
                background: #ff8210;
                min-width: 181px;
                height: 42px;
                display: inline-block;
                text-align: center;
                color: #fff;
                line-height: 39px;
                float: right;
                font-family: "Work Sans", Sans-serif;
                margin-top: -4px;
                font-size: 15px;
                font-weight: bold;
                text-transform: uppercase;
                text-decoration: none !important;
            }

            body > div.elementor.elementor-127.elementor-location-header > section.elementor-section.elementor-top-section.elementor-element.elementor-element-ad6f91f.header.elementor-reverse-mobile.elementor-section-boxed.elementor-section-height-default.elementor-section-height-default.jet-parallax-section > div.elementor-container.elementor-column-gap-default > div.elementor-column.elementor-col-50.elementor-top-column.elementor-element.elementor-element-11182f6.elementor-hidden-tablet > div > div.elementor-element.elementor-element-3eb9fbb.elementor-widget__width-auto.elementor-mobile-align-justify.elementor-widget-mobile__width-inherit.elementor-widget.elementor-widget-button > div > div {
                display: none;
            }

            .wp-core-ui .attachment.details .check, .wp-core-ui .attachment.selected .check:focus, .wp-core-ui .media-frame.mode-grid .attachment.selected .check {
                background-color: #2271b1 !important;
                box-shadow: 0 0 0 1px #fff, 0 0 0 2px #2271b1;
            }

            .media-menu-item active {
                background-color: #2271b1 !important;
            }

            .media-menu-item {
                background-color: #2271b1 !important;
            }

            div.media-frame-toolbar > div > div.media-toolbar-primary.search-form > button {
                background: #135e96 !important;
                border-color: #135e96 !important;
                color: #fff !important;
            }

            #menu-item-upload {
                background: #135e96 !important;
                border: none !important;
                color: #fff !important;
            }

            .load-more {
                display: none !important;
            }
        </style>
		<?php
	}

});

// associate subscriptions and meta
// no longer needed as the subscription id is associated when inserting on the thank you page
// instead redirect back to the editing page
// https://advancedforms.github.io/actions/af/form/submission/
// manually updates the terms of a post depending on the acf submission to accomodate client requests
add_action('af/form/submission', 'handle_form_submission', 10, 3);
function handle_form_submission($form, $field, $args)
{

	/** TEST AMOUNT SUBMITTED IS MORE THAN TOWNLIMIT */
    global $wpdb;
	$all_meta = get_post_meta($args['post']);
	$subscription_id = $all_meta['subscription_id'][0];
	$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id . "'";
	$order_details = $wpdb->get_results($sql);
	$order = wc_get_order($order_details[0]->order_id);
	$items = $order->get_items();
	foreach ($items as $item) {
		$product = $item->get_product();
	}
	$town_limit = get_post_meta($product->get_id(), 'town_limit', true);
	$total_count =
        count($field[0]['value']['area_selection_lockyer']) +
        count($field[0]['value']['area_selection_toowoomba']) +
        count($field[0]['value']['area_selection_south_burnett']) +
        count($field[0]['value']['area_selection_somerset']) +
        count($field[0]['value']['area_selection_western_downs']) +
        count($field[0]['value']['area_selection_southern_downs']) +
        count($field[0]['value']['area_selection_goondiwindi'])
    ;
	if ($total_count >= $town_limit + 1) {
        echo '<script>window.location="https://visitstayexplore.staging-sites.com.au/edit-business/?id='.$args['post'].'&message=limit_hit";</script>';
        header("Location: https://visitstayexplore.staging-sites.com.au/edit-business/?id=".$args['post']."&message=limit_hit");
        exit();
	}


    /** CLEAR ALL THE TERMS AND THEN UPDATE THEM FROM ACF */
    // empty it first and save
	wp_delete_object_term_relationships($args['post'], 'region');

    // get the slug from the term_id
    $term_to_update = get_term_by('id', $field[0]['value']['your_local_area'], 'region');

    // update the term region
    wp_set_object_terms($args['post'], $term_to_update->term_id, 'region', true);

    // get all terms for the post $args['post']
    $terms = get_the_terms($args['post'], 'region');

    /*
    echo '<pre>';
	print_r($terms);
	print_r($term_to_update);
	exit();
    */

	// update the post
	wp_update_post($args['post']);

    // regions
	$regions_to_update = $field[0]['value']['region_selection'];
	if (is_array($regions_to_update)) {
		foreach ($regions_to_update as $terms_array) {
			wp_set_post_terms($args['post'], $terms_array, 'region', true);
		}
	}

	// lockyer
	$areas_to_update_lockyer = $field[0]['value']['area_selection_lockyer'];
	if (is_array($areas_to_update_lockyer)) {
		foreach ($areas_to_update_lockyer as $terms_array) {
			wp_set_post_terms($args['post'], $terms_array, 'region', true);
		}
	}

    // tbah
	$areas_to_update_tbah = $field[0]['value']['area_selection_toowoomba'];
	if (is_array($areas_to_update_tbah)) {
		foreach ($areas_to_update_tbah as $terms_array) {
			wp_set_post_terms($args['post'], $terms_array, 'region', true);
		}
	}

	// area_selection_south_burnett
	$areas_to_update_southburnett = $field[0]['value']['area_selection_south_burnett'];
	if (is_array($areas_to_update_southburnett)) {
		foreach ($areas_to_update_southburnett as $terms_array) {
			wp_set_post_terms($args['post'], $terms_array, 'region', true);
		}
	}

	// area_selection_somerset
	$areas_to_update_somerset = $field[0]['value']['area_selection_somerset'];
	if (is_array($areas_to_update_somerset)) {
		foreach ($areas_to_update_somerset as $terms_array) {
			wp_set_post_terms($args['post'], $terms_array, 'region', true);
		}
	}

	// area_selection_western_downs
	$areas_to_update_westerndowns = $field[0]['value']['area_selection_western_downs'];
	if (is_array($areas_to_update_westerndowns)) {
		foreach ($areas_to_update_westerndowns as $terms_array) {
			wp_set_post_terms($args['post'], $terms_array, 'region', true);
		}
	}

	// area_selection_southern_downs
	$areas_to_update_southerndowns = $field[0]['value']['area_selection_southern_downs'];
	if (is_array($areas_to_update_southerndowns)) {
		foreach ($areas_to_update_southerndowns as $terms_array) {
			wp_set_post_terms($args['post'], $terms_array, 'region', true);
		}
	}

	// area_selection_goondiwindi
	$areas_to_update_goondiwindi = $field[0]['value']['area_selection_goondiwindi'];
	if (is_array($areas_to_update_goondiwindi)) {
		foreach ($areas_to_update_goondiwindi as $terms_array) {
			wp_set_post_terms($args['post'], $terms_array, 'region', true);
		}
	}

	// $current_terms = wp_get_post_terms( $args['post'], 'region'); dd('$current_terms'); dd($current_terms); exit();

	/** PUBLISH POST ALWAYS */
	if (is_numeric($args['post'])) {
		$saved_post = get_post($args['post']);
		$saved_post->post_status = 'publish';
		//$saved_post->post_name = $args['post']; // this saves the post name as the post id - which we dont want
		wp_update_post($saved_post);
		flush_rewrite_rules();
	}

}

/**
 * Add JS around the site
 */
add_action('wp_head', function () {
	?>
    <style>
        #review > div > div > p.rmp-rating-widget__msg.js-rmp-msg {
            background: #ff8210;
            color: white;
            border-radius: 10px;
            margin: 10px;
            padding: 10px;
        }

        #review {
            padding: 30px 0 !important;
            margin-bottom: 30px !important;
            text-align: center !important;
            border: 1px solid #ddd !important;
            border-radius: 10px !important;
            background: white !important;
            margin-top: 40px !important;
            padding-left: 20px !important;
            padding-right: 20px !important;
            border-radius: 0 !important;
            width: 421px !important;
        }
    </style>
	<?php
});

// get star rating of this comment
add_shortcode('display_star_rating', function ($atts) {
	global $wpdb;
	$plugin_dir = 'https://visitstayexplore.staging-sites.com.au/wp-content/plugins/preparemyproperty-custom-plugin/img';
	$comm_query = "SELECT * FROM  wp_rmp_analytics WHERE `post` = '" . $atts['comment_id'] . "'";
	$rating = $wpdb->get_results($comm_query);
	if (is_object($rating[0])) {
		$final_rating = $rating[0]->value;
	}
	for ($i = 1; $i <= $final_rating; $i++) {
		$star .= '<img src="' . $plugin_dir . '/star_rating.jpg" style="width:20px;">';
	}
	echo '<div style="max-width: 150px;float:left;margin-left:-5px;padding-bottom:5px;">' . $star . '</div>';
});

// get star rating of this comment
add_shortcode('display_star_rating_and_review_count', function ($atts) {
	global $wpdb;
	$plugin_dir = 'https://visitstayexplore.staging-sites.com.au/wp-content/plugins/preparemyproperty-custom-plugin/img';
	$average_rating = Rate_My_Post_Common::get_average_rating($atts['comment_id']);
	for ($i = 1; $i <= $average_rating; $i++) {
		$star .= '<img src="' . $plugin_dir . '/star_rating.jpg" style="width:20px;">';
	}
    echo '<div style="width: 150px;float:left;margin-left:-5px;padding-bottom:5px;">';
	if ($star) {
		echo '<div>' . $star . '</div>';
	}
	$existing_vote_count = Rate_My_Post_Common::get_vote_count($atts['comment_id']);
	if ($existing_vote_count) {
		echo '<div style="font-size:13px;line-height: 18px;">from ' . $existing_vote_count . ' Verified Recommendations</div>';
	}
    echo '</div>';
});

// enqueue the js
add_action('init', 'register_scripts');
function register_scripts()
{
	wp_enqueue_script('flag_message', plugins_url('js/flag_message.js', __FILE__), array('jquery'));
	wp_localize_script('flag_message', 'flag_message', array('url' => admin_url('admin-ajax.php')));
}

// shortcode to display the inappropiate message link
add_shortcode('display_flag_button', function ($atts) {
	$link = admin_url('admin-ajax.php?action=flag_comment');
	echo '<p style="text-align: right;margin-top:-15px;"><a href="' . $link . '" target="_blank" style="font-size:11px;" class="flag_comment" data-comment_id="' . $atts['comment_id'] . '" data-nonce="' . wp_create_nonce('display_flag_button') . '">Flag as Suspect or Inappropriate</a></p>';
	echo '<div id="flag_comment_message_' . $atts['comment_id'] . '" style="display:none;text-align: right;background-color: #ff8210;width: 200px;float: right;text-align: center;padding: 10px;padding-bottom: 0;border-radius: 10px;margin: 0;"><p style="color: white;padding: 5px;font-size: 12px;line-height: 15px;">Thank you for your concern! We will review the comment!</p></div>';
});

// add filter to display the shortcode after the text
add_filter( 'comment_text', 'customizing_comment_text', 20, 3 );
function customizing_comment_text( $comment_text, $comment, $args ) {
	$comment_text = do_shortcode('[display_flag_button]') . $comment_text;
	return $comment_text;
}

// hooks to send the message
add_action('wp_ajax_flag_comment', 'flag_comment');
add_action('wp_ajax_nopriv_flag_comment', 'flag_comment');
function flag_comment()
{
	global $wpdb;
	$query = "SELECT * from wp_comments WHERE comment_ID = " . $_REQUEST['comment_id'];
	$comm_query = $wpdb->get_results($query);

	// mail admin
	$message = "Hey PMP Admin Team,<br>Someone on your site has marked a comment as inappropiate. <br>The comment is \"" . $comm_query[0]->comment_content . "\". <br>Please login to the back of your site, and <a href=\"" . site_url() . "/wp-admin/comment.php?action=editcomment&c=" . $comm_query[0]->comment_ID . "\">click this link to review the comment</a>.<br>Thanks!'";
	wp_mail('contact@visitstayexplore.staging-sites.com.au', 'Someone Marked A Comment As Inappropiate', $message, array('Content-Type: text/html; charset=UTF-8'));

	// now return a succesful response and message to the frontend
	$response['success'] = true;
	echo json_encode($response);
	exit;
}

add_filter('woocommerce_checkout_logged_in_message', function ($message) {
	return 'Your message here';
});

/**
 * Modify the "must_log_in" string of the comment form.
 *
 * @see http://wordpress.stackexchange.com/a/170492/26350
 */
add_filter('comment_form_defaults', function ($fields) {
	$fields['must_log_in'] = '<p class="must-log-in">You must <a href="https://visitstayexplore.staging-sites.com.au/my-account/">Register</a> or <a href="https://visitstayexplore.staging-sites.com.au/my-account/">Login</a> to post a comment.</p>';
	return $fields;
});

// adds a dynamic button in the header
add_shortcode('ionline_dynamic_button', function () {
	if (is_user_logged_in()) {
		$link = '/my-account/my-listings/';
		$text = 'EDIT YOUR LISTINGS';
	} else {
		$link = '/subscriptions/';
		$text = 'ADD A NEW LISTING';
	}
	return '<a href="' . $link . '" style="background-color: #FF8210;color:white !important;padding:8px;padding-left:15px;padding-right:15px;font-weight:bold;float:right;">' . $text . '</a>';
});

// hooks to send the message
add_action('wp_ajax_listing_search_form', 'listing_search_form');
add_action('wp_ajax_nopriv_listing_search_form', 'listing_search_form');
function listing_search_form()
{
	wp_redirect('https://visitstayexplore.staging-sites.com.au/property-lists/?area_id=' . $_REQUEST['area_id'] . '&service_id=' . $_REQUEST['service_id'] . '&type=' . $_REQUEST['type']);
	exit();
}

/*
add_action('woocommerce_order_status_processing','ionline_test');
add_action('woocommerce_order_status_completed','ionline_test');
function ionline_test($order_id) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
*/

// hook to generate business listings on completed orders but NOT renewed subscriptions
// https://ionlineptyltd.teamwork.com/desk/tickets/8760434/messages
# add_action( 'woocommerce_order_status_completed', function ($order_id) {
add_action('woocommerce_order_status_processing', function ($order_id) {

    /** TESTS TO SEE IF ANY BUSINESS LISTING HAS THIS SUBSCRIPTION ID AND IF NOT MAKE A NEW BUSINESS LISTING */
	// get all posts called listings
	$args = array(
		'post_type' => 'listings',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC'
	);
	$all_listings = get_posts($args);

	// for each of the listings get the meta called 'subscription_id' and append it as a new meta called 'subscription_id'
	foreach ($all_listings as $listing) {
		$listing->subscription_id = get_post_meta($listing->ID, 'subscription_id', true);
	}

	// get the order by order_id, the subscription, then the related orders
	$order = wc_get_order($order_id);
	$subscriptions = wcs_get_subscriptions_for_order( $order_id , array( 'order_type' => array( 'any' ) ));
	$subscription = array_shift( $subscriptions );
	$related_subscriptions = $subscription->get_related_orders( 'all', 'renewal' );

	// for each of the related subscriptions make a new array with the key as the subscription_id
	foreach ($related_subscriptions as $key => $related_subscription) {
		$related_subscriptions_array[$key] = $key;
	}

	// loop through all the listings and test to see if the subscription_id matches any of the related subscriptions
	$found = false;
	foreach ($all_listings as $listing) {
		if (in_array($listing->subscription_id, $related_subscriptions_array)) {
			$found == true;
		}
	}

    if ($found == false) { // no business listings assigned against this order - so many a new one - otherwise do nothing..
	    // get the user
	    $user = wp_get_current_user(get_current_user_id());

	    // get the recently added order
	    $order = wc_get_order($order_id);
	    $items = $order->get_items();

	    // loop through the items and create draft posts for each one
	    foreach ($items as $item) {

		    // make a new business listing with the post_author as this user
		    $product = $item->get_product();
		    $args = array(
			    'post_type' => 'listings',
			    'post_author' => get_current_user_id(),
			    'status' => 'draft',
			    'post_title' => 'Order ID: ' . $order->get_id() . ' - Name: ' . $product->get_name() . ' ',
		    );
		    $inserted_id = wp_insert_post($args);
		    add_post_meta($inserted_id, 'subscription_id', $order->get_id()); // add the subscription_id meta to it
		    // $all_meta = get_post_meta($inserted_id); // test it
	    }
    }


});

// generates the proper url for qr codes
function url_origin($s, $use_forwarded_host = false)
{
	$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on');
	$sp = strtolower($s['SERVER_PROTOCOL']);
	$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
	$port = $s['SERVER_PORT'];
	$port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
	$host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
	$host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
	return $protocol . '://' . $host;
}

function full_url($s, $use_forwarded_host = false)
{
	return url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
}

// renders the star rating select box
add_shortcode('ionline_star_reviews', function ($post_id) {
	if (is_user_logged_in()) {
		$html = '';
		if ($_GET['success'] == 'false') {
			$html = '<span style="color:green;font-weight: bold;">You have already voted for this listing</span>';
		}
		if ($_GET['success'] == 'true') {
			$html = '<span style="color:green;font-weight: bold;">Thank you for your rating</span>';
		}
		if ($_GET['success'] == '') {
			$html .= 'How Many Stars (mandatory)?';
			$html .= '<form method="post" data-nonce="' . wp_create_nonce('update_star_rating_nonce') . '" data-post_id="' .  $post_id['post_id'] . '" action="https://visitstayexplore.staging-sites.com.au/wp-admin/admin-ajax.php?action=update_star_rating&post_id=' . $post_id['post_id'] . '" id="star_rating_ajax_form"><select id="star_rating_ajax" name="star_rating_ajax" style="width: 150px"><option value="5">5 Star</option><option value="4">4 Stars</option><option value="3">3 Stars</option><option value="2">2 Stars</option><option value="1">1 Star</option><option value="0">No Stars</option></select></form>';
		}
		echo '<span id="ionline_star_reviews" style="margin-top:-5px;" >' . $html . '</span>';
	}
});

// updates the post meta for the star rating for this post
add_action('wp_ajax_update_star_rating', 'update_star_rating');
add_action('wp_ajax_nopriv_update_star_rating', 'update_star_rating');
function update_star_rating()
{

    global $wpdb;
	if (!is_numeric($_REQUEST['post_id'])) {
		return false;
	}
	if (!is_numeric($_REQUEST['star_rating_ajax'])) {
		return false;
	}

	$data = [];
	$post_id = $_REQUEST['post_id'];
	$rating = $_REQUEST['star_rating_ajax'];
	$post = get_post($_REQUEST['post_id']);

    /*
	// set an array of users that have already voted
	update_post_meta($post_id, 'users_updated', array(get_current_user_id()));
	$user_updated = get_post_meta($post->ID, 'users_updated', true);
	if (in_array(get_current_user_id(), $user_updated)) {
		$data['success'] = false;
	} else {

	}
    */

	if (!add_post_meta($post_id, 'rmp_vote_count', 1, true)) {
		$existing_vote_count = Rate_My_Post_Common::get_vote_count($post_id);
		$new_vote_count = intval($existing_vote_count + 1);
		update_post_meta($post_id, 'rmp_vote_count', $new_vote_count);
	}

	$average_rating = Rate_My_Post_Common::get_average_rating($post_id);
	$post_meta = update_post_meta($post_id, 'rmp_avg_rating', $average_rating);

	if (!add_post_meta($post_id, 'rmp_rating_val_sum', $rating, true)) {
		$existing_ratings_sum = Rate_My_Post_Common::get_sum_of_ratings($post_id);
		$new_ratings_sum = intval($existing_ratings_sum + $rating);
		update_post_meta($post_id, 'rmp_rating_val_sum', $new_ratings_sum);
	}

	$data['success'] = true;

    // star_rating_ajax

    return json_encode($data);

}

// js to listen for the event
add_action('wp_footer', function () {
	?>
    <script>

        jQuery(document).ready(function () {

            // ratings table info in wp_post_meta
            // admin_ajax function is run called 'process_rating' in class-rate-my-post-public.php
            // rmp_vote_count
            // rmp_avg_rating
            // rmp_rating_val_sum

            /** MOVE THE STAR RATING TO BEFORE THE SUBMIT BUTTON */
            // get the html
            var star_html = jQuery('#ionline_star_reviews').html();

            // remove it from the dom
            jQuery('#ionline_star_reviews').remove();

            // append it after the form
            jQuery('.comment-form-comment').after(star_html);

            // add some padding around the button
            jQuery('.form-submit').css('margin-top', 20);

            // add some text to the comments field
            jQuery('#commentform > p.comment-form-comment > label').empty().html('Let others know why you recommend this business (mandatory)');

            // run the ajax call in the to submit the review rating, then submit the post
            jQuery('#submit').on('click', function (e) {
                // e.preventDefault();
                var star_rating_ajax = jQuery('#star_rating_ajax').val();
                var nonce = jQuery('#star_rating_ajax_form').attr("data-nonce");
                var post_id = jQuery('#star_rating_ajax_form').attr("data-post_id");

                jQuery.ajax({
                    type : 'post',
                    dataType : 'json',
                    url : 'https://visitstayexplore.staging-sites.com.au/wp-admin/admin-ajax.php',
                    data : { action: 'update_star_rating', star_rating_ajax : star_rating_ajax, post_id : post_id, nonce: nonce },
                    success: function(response) {
                        console.log(response);
                    }
                })

                jQuery.ajax({
                    type : 'post',
                    dataType : 'json',
                    url : 'https://visitstayexplore.staging-sites.com.au/wp-admin/admin-ajax.php',
                    data : { action: 'process_rating', duration : 1, postID : post_id, star_rating : star_rating_ajax },
                    success: function(response) {
                        console.log(response);
                    }
                })

            });

            // trigger the click
            jQuery(this).trigger('click');

        });
    </script>
	<?php
});

// ionline custom error notice display
add_shortcode('ionline_woocommerce_notices', function ($attrs) {
	if (wc_notice_count() > 0) {
		?>
        <div class="woocommerce-notices-shortcode woocommerce">
			<?php wc_print_notices(); ?>
        </div>
		<?php
	}
});

// adjusts the query made by acf so that depending on the selections made on the business editing page, it will display parent or child taxonomies only
// note this section has to be hard coded as there is no way to dynamically hide/display parent/child taxonomies
// display error if they are trying to add more than what they are allowed
// applying it to all fields when i could have just targetted a single one - oh well
add_filter('acf/fields/taxonomy/query', 'change_taxt_outcomes_in_acf', 10, 3);
function change_taxt_outcomes_in_acf($args, $field, $post_id)
{

    global $wpdb;

	// only display parents in the region selection
	if ('region_selection' === $field['_name']) {
		$args['parent'] = 0;
	}

	// only display the children of lockyer - tax id - 26
	if ('area_selection_lockyer' === $field['_name']) {

        // set the taxonomy parent
		$args['parent'] = '26';

		// get the max allowed for the listing
		$all_meta = get_post_meta($post_id);
		$subscription_id = $all_meta['subscription_id'][0];
		$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id . "'";
		$order_details = $wpdb->get_results($sql);
		$order = wc_get_order($order_details[0]->order_id);
		$items = $order->get_items();
		foreach ($items as $item) {
			$product = $item->get_product();
		}
		$town_limit = get_post_meta($product->get_id(), 'town_limit', true);

		// count the number of child taxonomies selected for this listing
		$allocated_areas = wp_get_post_terms( $post_id, 'region');
        $number_of_allocated = count($allocated_areas);

        // compare and return false
        if ($number_of_allocated >= $town_limit) {
            exit();
        }
	}

	// only display the children of tbah  - tax id - 24
	if ('area_selection_toowoomba' === $field['_name']) {

		// set the taxonomy parent
		$args['parent'] = '24';

		// get the max allowed for the listing
		$all_meta = get_post_meta($post_id);
		$subscription_id = $all_meta['subscription_id'][0];
		$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id . "'";
		$order_details = $wpdb->get_results($sql);
		$order = wc_get_order($order_details[0]->order_id);
		$items = $order->get_items();
		foreach ($items as $item) {
			$product = $item->get_product();
		}
		$town_limit = get_post_meta($product->get_id(), 'town_limit', true);

		// count the number of child taxonomies selected for this listing
		$allocated_areas = wp_get_post_terms( $post_id, 'region');
		$number_of_allocated = count($allocated_areas);

		// compare and return false
		if ($number_of_allocated >= $town_limit) {
			exit();
		}
	}

	// only display the children of south burnett  - tax id - 351
	if ('area_selection_south_burnett' === $field['_name']) {

		$args['parent'] = '351';
		// set the taxonomy parent

		// get the max allowed for the listing
		$all_meta = get_post_meta($post_id);
		$subscription_id = $all_meta['subscription_id'][0];
		$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id . "'";
		$order_details = $wpdb->get_results($sql);
		$order = wc_get_order($order_details[0]->order_id);
		$items = $order->get_items();
		foreach ($items as $item) {
			$product = $item->get_product();
		}
		$town_limit = get_post_meta($product->get_id(), 'town_limit', true);

		// count the number of child taxonomies selected for this listing
		$allocated_areas = wp_get_post_terms( $post_id, 'region');
		$number_of_allocated = count($allocated_areas);

		// compare and return false
		if ($number_of_allocated >= $town_limit) {
			exit();
		}
	}

	// only display the children of south burnett  - tax id - 355
	if ('area_selection_somerset' === $field['_name']) {

		// set the taxonomy parent
		$args['parent'] = '355';

		// get the max allowed for the listing
		$all_meta = get_post_meta($post_id);
		$subscription_id = $all_meta['subscription_id'][0];
		$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id . "'";
		$order_details = $wpdb->get_results($sql);
		$order = wc_get_order($order_details[0]->order_id);
		$items = $order->get_items();
		foreach ($items as $item) {
			$product = $item->get_product();
		}
		$town_limit = get_post_meta($product->get_id(), 'town_limit', true);

		// count the number of child taxonomies selected for this listing
		$allocated_areas = wp_get_post_terms( $post_id, 'region');
		$number_of_allocated = count($allocated_areas);

		// compare and return false
		if ($number_of_allocated >= $town_limit) {
			exit();
		}
	}

	// only display the children of south burnett  - tax id - 365
	if ('area_selection_western_downs' === $field['_name']) {

		// set the taxonomy parent
		$args['parent'] = '365';

		// get the max allowed for the listing
		$all_meta = get_post_meta($post_id);
		$subscription_id = $all_meta['subscription_id'][0];
		$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id . "'";
		$order_details = $wpdb->get_results($sql);
		$order = wc_get_order($order_details[0]->order_id);
		$items = $order->get_items();
		foreach ($items as $item) {
			$product = $item->get_product();
		}
		$town_limit = get_post_meta($product->get_id(), 'town_limit', true);

		// count the number of child taxonomies selected for this listing
		$allocated_areas = wp_get_post_terms( $post_id, 'region');
		$number_of_allocated = count($allocated_areas);

		// compare and return false
		if ($number_of_allocated >= $town_limit) {
			exit();
		}
	}

	// only display the children of southern downs  - tax id - 387
	if ('area_selection_southern_downs' === $field['_name']) {

		// set the taxonomy parent
		$args['parent'] = '387';

		// get the max allowed for the listing
		$all_meta = get_post_meta($post_id);
		$subscription_id = $all_meta['subscription_id'][0];
		$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id . "'";
		$order_details = $wpdb->get_results($sql);
		$order = wc_get_order($order_details[0]->order_id);
		$items = $order->get_items();
		foreach ($items as $item) {
			$product = $item->get_product();
		}
		$town_limit = get_post_meta($product->get_id(), 'town_limit', true);

		// count the number of child taxonomies selected for this listing
		$allocated_areas = wp_get_post_terms( $post_id, 'region');
		$number_of_allocated = count($allocated_areas);

		// compare and return false
		if ($number_of_allocated >= $town_limit) {
			exit();
		}
	}

	// only display the children of goondowindi  - tax id - 390
	if ('area_selection_goondiwindi' === $field['_name']) {

		// set the taxonomy parent
		$args['parent'] = '390';

		// get the max allowed for the listing
		$all_meta = get_post_meta($post_id);
		$subscription_id = $all_meta['subscription_id'][0];
		$sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` = '" . $subscription_id . "'";
		$order_details = $wpdb->get_results($sql);
		$order = wc_get_order($order_details[0]->order_id);
		$items = $order->get_items();
		foreach ($items as $item) {
			$product = $item->get_product();
		}
		$town_limit = get_post_meta($product->get_id(), 'town_limit', true);

		// count the number of child taxonomies selected for this listing
		$allocated_areas = wp_get_post_terms( $post_id, 'region');
		$number_of_allocated = count($allocated_areas);

		// compare and return false
		if ($number_of_allocated >= $town_limit) {
			exit();
		}
	}

    // only display the terms relevant in this region
    /*
	if ('region_selection' === $field['_name']) {
        echo '<pre>';
        print_r($args);
        print_r($field);
        print_r($post_id);
        exit();
	}
    */

	// echo '<pre>';print_r($args);
	return $args;
}

function object_to_array($data)
{
	if (is_array($data) || is_object($data))
	{
		$result = [];
		foreach ($data as $key => $value)
		{
			$result[$key] = (is_array($value) || is_object($value)) ? object_to_array($value) : $value;
		}
		return $result;
	}
	return $data;
}

// action to create a draft business listing when admin creates a new subscription
add_action('woocommerce_subscription_status_active', function ($subscription) {

    if (is_admin()) {

        $subscription_id = $subscription->get_id();
        $customer_id = $subscription->get_customer_id();
        $order = $subscription->get_parent();
	    $items = $order->get_items();

	    // loop through the items and create draft posts for each one
	    foreach ($items as $item) {

		    // make a new business listing with the post_author as this user
		    $product = $item->get_product();
		    $args = array(
			    'post_type' => 'listings',
			    'post_author' => $customer_id,
			    'status' => 'draft',
			    'post_title' => 'Order ID: ' . $order->get_id() . ' - Name: ' . $product->get_name() . ' ',
		    );
		    $inserted_id = wp_insert_post($args);
		    add_post_meta($inserted_id, 'subscription_id', $order->get_id()); // add the subscription_id meta to it
		    // $all_meta = get_post_meta($inserted_id); // test it
	    }

    }
});

// display custom meta box for subscription_id in the admin for the post type listings
add_action('add_meta_boxes', function () {
    add_meta_box('subscription_id', 'Subscription ID', 'subscription_id_meta_box', 'listings', 'side', 'default');
});

// add the callback to the meta box
function subscription_id_meta_box()
{
    global $post;
    $subscription_id = get_post_meta($post->ID, 'subscription_id', true);
    echo '<p>' . $subscription_id . '</p>';
}

// add a text field for the post type listings to update the meta field subscription_id
add_action('save_post', function ($post_id) {
    if (isset($_POST['subscription_id'])) {
        update_post_meta($post_id, 'subscription_id', $_POST['subscription_id']);
    }
});

// hide subscription box on edit screen
add_action('wp_head', function () {
    global $post;
    if ($post->ID == 1316) {
        ?>
        <style>
            .af-field.af-field-type-text.af-field-subscription-id.acf-field.acf-field-text.acf-field-62b8f751fc000 {
                display: none;
            }
        </style>
        <?php
    }
});

// change the wording on the comments post button
add_filter('comment_form_defaults', 'wpsites_change_comment_form_submit_label');
function wpsites_change_comment_form_submit_label($arg) {
	$arg['label_submit'] = 'Submit';
	return $arg;
}

// hide product image
add_action('wp_head' , function () {
    global $post;
    if ($post->ID == '2610') {
        ?>
        <style>
            .woocommerce-product-gallery.woocommerce-product-gallery--without-images.woocommerce-product-gallery--columns-4.images {
                display: none !important;
            }
        </style>
        <?php
    }
});

// add the business listings to this table
add_action('manage_shop_subscription_posts_custom_column', 'wpsites_add_new_subscription_column_content', 10, 2);
function wpsites_add_new_subscription_column_content($column, $post_id) {
    if ($column == 'order_items') {

        // get all posts with the type 'listings' that have the subscription_id meta field set to this subscription id
	    $order = wc_get_order($post_id);
        $args = array(
            'post_type' => 'listings',
            'meta_query' => array(
                array(
                    'key' => 'subscription_id',
                    'value' => $order->get_parent_id(),
                ),
            ),
        );
        $listings = get_posts($args);

        // if the listings count is greater than 0, loop through them and display them as a button to the admin
        if (count($listings) > 0) {
            foreach ($listings as $listing) {
                // style the link below as a wordpress admin button
                // limit the lengeth of the post title to 80 characters and have a ... at the end if it is longer

                echo '<a href="' . get_edit_post_link($listing->ID) . '" class="button" style="margin-top:5px;margin-bottom:5px;" >' . make_pretty_title($listing->post_title) . '</a><br>';
            }
        }

    }
}

function make_pretty_title($something) {
	$something = substr($something, 0, 25);
	if (strlen($something) > 24) {
		$something .= '..';
	}
    return $something;
}

// with jquery update the html of this element #order_items
add_action('admin_head', function () {
    // if the url contains the words shop_subscription
    if (strpos($_SERVER['REQUEST_URI'], 'shop_subscription') !== false) {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                $('#order_items').html('<p style="font-size: 14px;">Type / Business Listing</p>');
            });
        </script>
        <?php
    }
});

// add a new column to the admin table for the url https://visitstayexplore.staging-sites.com.au/wp-admin/edit.php?post_type=listings
add_filter('manage_listings_posts_columns', 'manage_listings_posts_columns_ionline');
function manage_listings_posts_columns_ionline($columns) {
    $columns['business_listings'] = 'Business Listings';
    return $columns;
}

// if the column is called Business Listsings, display the listings
add_action('manage_listings_posts_custom_column', 'manage_listings_posts_custom_column_ionline', 10, 2);
function manage_listings_posts_custom_column_ionline($column, $post_id) {
    if ($column == 'business_listings') {
        $listing = get_post($post_id);
        $meta = get_post_meta($post_id);
        if (isset($meta['subscription_id'])) {
            if (is_array($meta['subscription_id'])) {
	            echo '<a href="' . get_edit_post_link($meta['subscription_id'][0]) . '" class="button" style="margin-top:5px;margin-bottom:5px;" >View Subscription</a><br>';
            }
        }
    }
}

// make the column business_listings the second column in the table
add_filter('manage_edit-listings_sortable_columns', 'manage_edit_listings_sortable_columns_ionline');
function manage_edit_listings_sortable_columns_ionline($columns) {
    $columns['business_listings'] = 'business_listings';
    return $columns;
}
/*
// if we are on the listings page, sort the listings by the business_listings column
add_filter('manage_posts_columns', 'column_order');
function column_order($columns) {
	if (strpos($_SERVER['REQUEST_URI'], 'listings') !== false) {
		$n_columns = array();
		$move = 'business_listings'; // what to move
		$before = 'author'; // move before this
		foreach($columns as $key => $value) {
			if ($key==$before){
				$n_columns[$move] = $move;
			}
			$n_columns[$key] = $value;
		}
		return $n_columns;
	}

}*/

// hide some fields on the user editing listing screen
add_action('wp_head', function () {
    if (is_page('edit-business')) {
        ?>
        <style>
            .hide_frontend {
                display: none;
            }
        </style>
        <?php
    }
});

// when the backend post is saved, also save all the meta fields
add_action( 'save_post', 'post_updated_ionline', 10, 3 );
function post_updated_ionline( $post_id, $post_after, $post_before ) {

    // remove all terms for this post
    wp_delete_object_term_relationships( $post_id, 'region' );

	$all_acf = get_fields($post_id);

	// get the slug from the term_id
	$term_to_update = get_term_by('id', $all_acf['listing_information_group']['your_local_area'], 'region');

	// update the term region
	wp_set_object_terms($post_id, $term_to_update->term_id, 'region', true);

	$terms = get_the_terms($post_id, 'region');

	// regions
	$regions_to_update = $all_acf['listing_information_group']['region_selection'][0];
	if (is_array($regions_to_update)) {
		foreach ($regions_to_update as $terms_array) {
			wp_set_post_terms($post_id, $terms_array, 'region', true);
		}
	}
	if (is_numeric($regions_to_update)) {
		wp_set_post_terms($post_id, $terms_array, 'region', true);
	}

	// lockyer
    // remove all the terms from the lockyer taxonomy
	$areas_to_update_lockyer = $all_acf['listing_information_group']['area_selection_lockyer'];
	if (is_array($areas_to_update_lockyer)) {
		foreach ($areas_to_update_lockyer as $terms_array) {
			wp_set_post_terms($post_id, $terms_array, 'region', true);
		}
	}
	if (is_numeric($areas_to_update_lockyer)) {
		wp_set_post_terms($post_id, $terms_array, 'region', true);
	}

	// tbah
	$areas_to_update_tbah = $all_acf['listing_information_group']['area_selection_toowoomba'];
	if (is_array($areas_to_update_tbah)) {
		foreach ($areas_to_update_tbah as $terms_array) {
			wp_set_post_terms($post_id, $terms_array, 'region', true);
		}
	}
	if (is_numeric($areas_to_update_tbah)) {
		wp_set_post_terms($post_id, $terms_array, 'region', true);
	}

	// south burnett
	$areas_to_update_sburnett = $all_acf['listing_information_group']['area_selection_south_burnett'];
	if (is_array($areas_to_update_sburnett)) {
		foreach ($areas_to_update_sburnett as $terms_array) {
			wp_set_post_terms($post_id, $terms_array, 'region', true);
		}
	}
	if (is_numeric($areas_to_update_sburnett)) {
		wp_set_post_terms($post_id, $terms_array, 'region', true);
	}

	// sommerset
	$areas_to_update_sommerset = $all_acf['listing_information_group']['area_selection_somerset'];
	if (is_array($areas_to_update_sommerset)) {
		foreach ($areas_to_update_sommerset as $terms_array) {
			wp_set_post_terms($post_id, $terms_array, 'region', true);
		}
	}
	if (is_numeric($areas_to_update_sommerset)) {
		wp_set_post_terms($post_id, $terms_array, 'region', true);
	}

	// western downs
	$areas_to_update_westerndowns = $all_acf['listing_information_group']['area_selection_western_downs'];
	if (is_array($areas_to_update_westerndowns)) {
		foreach ($areas_to_update_westerndowns as $terms_array) {
			wp_set_post_terms($post_id, $terms_array, 'region', true);
		}
	}
	if (is_numeric($areas_to_update_westerndowns)) {
		wp_set_post_terms($post_id, $terms_array, 'region', true);
	}

	// southern downs
	$areas_to_update_southerdowns = $all_acf['listing_information_group']['area_selection_southern_downs'];
	if (is_array($areas_to_update_southerdowns)) {
		foreach ($areas_to_update_southerdowns as $terms_array) {
			wp_set_post_terms($post_id, $terms_array, 'region', true);
		}
	}
	if (is_numeric($areas_to_update_southerdowns)) {
		wp_set_post_terms($post_id, $terms_array, 'region', true);
	}

	// goondiwindi
	$areas_to_update_goondiwindi = $all_acf['listing_information_group']['area_selection_goondiwindi'];
	if (is_array($areas_to_update_goondiwindi)) {
		foreach ($areas_to_update_goondiwindi as $terms_array) {
			wp_set_post_terms($post_id, $terms_array, 'region', true);
		}
	}
	if (is_numeric($areas_to_update_goondiwindi)) {
		wp_set_post_terms($post_id, $terms_array, 'region', true);
	}

}

// action to send notification when event submitted - supplied by vendor
add_action( 'transition_post_status', function ( $new_status, $old_status, $post ) {
	// Early bail: We are looking for published posts only
	if ( $new_status != 'publish' ) {
		return;
	}

	// Early bail: Unexpected value
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	// Early bail: We need the "tribe" function to be loaded
	if ( ! function_exists( 'tribe' ) ) {
		return;
	}

	$main = tribe( 'community.main' );

	// Early bail: We could not get the desired class from the Container
	if ( ! $main instanceof Tribe__Events__Community__Main ) {
		return;
	}

	$default_status = $main->getOption( 'defaultStatus' );

	// Early bail: Old status is not the default status
	if ( $old_status != $default_status ) {
		return;
	}

	// Early bail: We are just interested in Events
	if ( $post->post_type != 'tribe_events' ) {
		return;
	}

	$author = get_user_by( 'id', $post->post_author );

	// Early bail: Author not available (eg: Anonymous submission)
	if ( ! $author instanceof WP_User ) {
		return;
	}

	$email = $author->user_email;

	// Early bail: Invalid email
	if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		return;
	}

	$subject = sprintf( 'Congratulations! %s was published!', $post->post_title );
	$message = sprintf( 'Congratulations, your event was published! Click <a href="%s">here</a> to view it!', esc_url( get_post_permalink( $post ) ) );

	wp_mail(
		$email,
		esc_html__( $subject, 'tribe-events-community' ),
		__( wp_kses_post_deep( $message ), 'tribe-events-community' ),
		[ 'Content-Type: text/html; charset=UTF-8' ]
	);

}, 10, 3 );

// generate seo friendly permalinks for listings based on listing meta region
add_filter('post_type_link', 'listing_permalink_structure', 1, 2);
function listing_permalink_structure($post_link, $post) {

    if (is_object($post) && $post->post_type == 'listings') {

        $primary_region_term_id = get_post_meta($post->ID, 'listing_information_group_your_local_area', true);
        $primary_region = get_term($primary_region_term_id);

        $sluggish = [];

        // get the parent/child slugs
        if ($primary_region->parent == 0) {
            $sluggish[] = $primary_region->slug;
        } else {
            $sluggish[] = get_term($primary_region->parent)->slug;
            $sluggish[] = $primary_region->slug;
        }

        if (count($sluggish) > 0) {
            $region_slug = implode('/', $sluggish);
            $path = '/listings/' . $region_slug . '/' . $post->post_name . '/';
            return home_url($path);
        }
    }

    return $post_link;
}

// rewrite rules
// add_action('init', 'listing_seo_rewrite_rules');
function listing_seo_rewrite_rules() {
    // For URLs with parent/child regions: /listings/toowoomba-region/toowoomba/listing-name/
    add_rewrite_rule(
            '^listings/([^/]+)/([^/]+)/([^/]+)/?$',
            'index.php?post_type=listings&name=$matches[3]',
            'top'
    );

    // For URLs with single region: /listings/single-region/listing-name/
    add_rewrite_rule(
            '^listings/([^/]+)/([^/]+)/?$',
            'index.php?post_type=listings&name=$matches[2]',
            'top'
    );
}

add_action('init', 'service_listings_rewrite_rules');
function service_listings_rewrite_rules() {
    // Handle /service-listings/region-slug/area-slug/service-slug/listing-slug/
    add_rewrite_rule(
            '^service-listings/([^/]+)/([^/]+)/([^/]+)/([^/]+)/?$',
            'index.php?pagename=service-listings&region_slug=$matches[1]&area_slug=$matches[2]&service_slug=$matches[3]&listing_slug=$matches[4]',
            'top'
    );

    // Handle /service-listings/region-slug/area-slug/service-slug/
    add_rewrite_rule(
            '^service-listings/([^/]+)/([^/]+)/([^/]+)/?$',
            'index.php?pagename=service-listings&region_slug=$matches[1]&area_slug=$matches[2]&service_slug=$matches[3]',
            'top'
    );

    // Handle /service-listings/region-slug/area-slug/
    add_rewrite_rule(
            '^service-listings/([^/]+)/([^/]+)/?$',
            'index.php?pagename=service-listings&region_slug=$matches[1]&area_slug=$matches[2]',
            'top'
    );

    // Handle /service-listings/region-slug/
    add_rewrite_rule(
            '^service-listings/([^/]+)/?$',
            'index.php?pagename=service-listings&region_slug=$matches[1]',
            'top'
    );
}

// Register the query var
add_filter('query_vars', 'service_listings_query_vars');
function service_listings_query_vars($vars) {
    $vars[] = 'region_slug';
    $vars[] = 'service_slug';
    $vars[] = 'area_slug';
    $vars[] = 'listing_slug';
    $vars[] = 'type';
    return $vars;
}

// Prevent canonical redirect for service-listings URLs
add_filter('redirect_canonical', 'prevent_service_listings_redirect', 10, 2);
function prevent_service_listings_redirect($redirect_url, $requested_url) {
    // Check if this is a service-listings URL
    if (strpos($requested_url, '/service-listings/') !== false) {
        // Don't redirect service-listings URLs
        return false;
    }
    return $redirect_url;
}

add_action('init', function() {
//     flush_rewrite_rules();
}, 999);

add_action('init', function () {
//     remove_spam_customer_users();
});

function preview_spam_customer_removal()
{
    global $wp;

    // Check if WooCommerce is active
    if (!function_exists('wc_get_orders')) {
        return new WP_Error('woocommerce_missing', 'WooCommerce is not active');
    }

    // Get all users with 'customer' role
    $customer_users = get_users(array(
            'role' => 'customer',
            'fields' => 'all',
            'number' => -1
    ));

    $would_delete = array();
    $would_keep = array();

    foreach ($customer_users as $user) {
        // Check if user has any orders
        $orders = wc_get_orders(array(
                'customer_id' => $user->ID,
                'limit' => 1,
                'return' => 'ids'
        ));

        $user_info = array(
                'id' => $user->ID,
                'email' => $user->user_email,
                'username' => $user->user_login,
                'registered' => $user->user_registered
        );

        if (empty($orders)) {
            $would_delete[] = $user_info;
        } else {
            $would_keep[] = $user_info;
        }
    }

    dd(array(
        'total_customers' => count($customer_users),
        'would_delete_count' => count($would_delete),
        'would_keep_count' => count($would_keep),
        'would_delete' => $would_delete,
        'would_keep' => $would_keep
    ));

    exit();

}

function remove_spam_customer_users()
{
    // Check if WooCommerce is active
    if (!function_exists('wc_get_orders')) {
        return new WP_Error('woocommerce_missing', 'WooCommerce is not active');
    }

    // Get all users with 'customer' role
    $customer_users = get_users(array(
            'role' => 'customer',
            'fields' => 'all',
            'number' => -1 // Get all users
    ));

    $deleted_count = 0;
    $skipped_count = 0;
    $deleted_users = array();

    foreach ($customer_users as $user) {
        // Check if user has any orders
        $orders = wc_get_orders(array(
                'customer_id' => $user->ID,
                'limit' => 1,
                'return' => 'ids'
        ));

        // If no orders found, delete the user
        if (empty($orders)) {
            // Include the wp-admin/includes/user.php file for wp_delete_user function
            require_once(ABSPATH . 'wp-admin/includes/user.php');

            // Delete user and reassign their content to admin (ID 1) if any
            $result = wp_delete_user($user->ID, 1);

            if ($result) {
                $deleted_count++;
                $deleted_users[] = array(
                        'id' => $user->ID,
                        'email' => $user->user_email,
                        'username' => $user->user_login
                );
            }
        } else {
            $skipped_count++;
        }
    }

    return array(
            'success' => true,
            'deleted' => $deleted_count,
            'skipped' => $skipped_count,
            'deleted_users' => $deleted_users
    );
}

add_action('wp_enqueue_scripts', 'enqueue_edit_listing_scripts_styles');
function enqueue_edit_listing_scripts_styles() {
    if (is_page('edit-listing')) {

        wp_enqueue_style('tomselect','https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css');
        wp_enqueue_script('tomselect','https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js');
        // wp_enqueue_script('tomselect-custom', plugins_url('js/tomselect.js', __FILE__), array('jquery'));
        wp_enqueue_script('tailwind','https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4');
        wp_enqueue_style('edit-listing', plugin_dir_url(__FILE__) . 'css/edit-listing.css?'.uniqid());



        wp_enqueue_script('flag_message', plugins_url('js/flag_message.js', __FILE__), array('jquery'));
    }
}

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// @todo: make sure that the field names line up with acf

/**
 * Handle image upload to WordPress media library
 *
 * @param string $file_key The $_FILES array key
 * @param int $listing_id The post ID to attach the image to
 * @return int|WP_Error The attachment ID on success, WP_Error on failure
 */
function handle_image_upload($file_key, $listing_id) {
    // Check if file was uploaded
    if (empty($_FILES[$file_key]['name'])) {
        return new WP_Error('no_file', 'No file was uploaded.');
    }

    // Validate file type
    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/webp');
    $file_type = $_FILES[$file_key]['type'];

    if (!in_array($file_type, $allowed_types)) {
        return new WP_Error('invalid_type', 'Only JPG, PNG, and WebP images are allowed.');
    }

    // Validate file size (5MB max)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($_FILES[$file_key]['size'] > $max_size) {
        return new WP_Error('file_too_large', 'File size must be less than 5MB.');
    }

    // WordPress upload handling
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    // Handle the upload
    $upload = wp_handle_upload($_FILES[$file_key], array('test_form' => false));

    if (isset($upload['error'])) {
        return new WP_Error('upload_error', $upload['error']);
    }

    // Prepare attachment data
    $attachment = array(
        'post_mime_type' => $upload['type'],
        'post_title'     => sanitize_file_name($upload['file']),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    // Insert attachment into media library
    $attachment_id = wp_insert_attachment($attachment, $upload['file'], $listing_id);

    if (is_wp_error($attachment_id)) {
        return $attachment_id;
    }

    // Generate attachment metadata
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
    wp_update_attachment_metadata($attachment_id, $attachment_data);

    return $attachment_id;
}

// add wp ajax action to process basics_form_save_action
add_action('wp_ajax_edit_listing', 'edit_listing_callback');
add_action('wp_ajax_nopriv_edit_listing', 'edit_listing_callback');
function edit_listing_callback() {

    // check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'edit_listing_nonce')) {
        return;
    }

    // sanitize everything...
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            $data[$key] = array_map('sanitize_text_field', $value);
        } else {
            $data[$key] = sanitize_text_field($value);
        }
    }

    $errorBag = [];
    $successMsg = null;
    switch ($data['section'] ?? 'basics') {
        case 'basics':
            if (empty($data['business_name'])) {
                $errorBag['business_name'] = 'Business name is required.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_business_name', $data['business_name']);

            if (empty($data['region'])) {
                $errorBag['region'] = 'Region is required.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_region', $data['region']);

            if (empty($data['introduction'])) {
                $errorBag['introduction'] = 'Introduction is required.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_introduction', $data['introduction']);

            if (empty($data['localarea'])) {
                $errorBag['localarea'] = 'Local Area is required.';
            }
            $localarea_value = is_array($data['localarea']) ? $data['localarea'][0] : $data['localarea'];
            update_post_meta($data['listing_id'], $data['section'].'_localarea', json_encode($localarea_value));

            if (empty($data['additionalareas'])) {
                $errorBag['additionalareas'] = 'Additional Areas is required.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_additionalareas', json_encode($data['additionalareas']));

            if (empty($data['services'])) {
                $errorBag['services'] = 'Service Selection is required.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_services', json_encode($data['services']));

            if (empty($data['licences'])) {
                $errorBag['licences'] = 'Licences is required.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_licences', $data['licences']);

            break;
        case 'branding':
            // Handle logo upload
            if (isset($_FILES['logo']) && !empty($_FILES['logo']['name'])) {
                $logo_id = handle_image_upload('logo', $data['listing_id']);
                if (is_wp_error($logo_id)) {
                    $errorBag['logo'] = $logo_id->get_error_message();
                } else {
                    update_post_meta($data['listing_id'], $data['section'].'_logo', $logo_id);
                }
            } elseif (empty(get_post_meta($data['listing_id'], $data['section'].'_logo', true))) {
                $errorBag['logo'] = 'Logo is required.';
            }

            // Handle hero banner upload
            if (isset($_FILES['hero_banner']) && !empty($_FILES['hero_banner']['name'])) {
                $hero_id = handle_image_upload('hero_banner', $data['listing_id']);
                if (is_wp_error($hero_id)) {
                    $errorBag['hero_banner'] = $hero_id->get_error_message();
                } else {
                    update_post_meta($data['listing_id'], $data['section'].'_hero_banner', $hero_id);
                }
            } elseif (empty(get_post_meta($data['listing_id'], $data['section'].'_hero_banner', true))) {
                $errorBag['hero_banner'] = 'Hero banner is required.';
            }

            // Handle gallery uploads (multiple images) - stored in ACF field 'branding_gallery'
            $gallery_field_name = $data['section'] . '_gallery';
            $existing_gallery = get_field($gallery_field_name, $data['listing_id']);

            // ACF Gallery returns an array - normalize to just IDs
            $gallery_ids_array = array();
            if (is_array($existing_gallery)) {
                foreach ($existing_gallery as $item) {
                    if (is_array($item) && isset($item['ID'])) {
                        // ACF returns array format with ID key
                        $gallery_ids_array[] = (int) $item['ID'];
                    } elseif (is_numeric($item)) {
                        // Just in case ACF is set to return IDs only
                        $gallery_ids_array[] = (int) $item;
                    }
                }
            }

            // Track if any changes were made to the gallery
            $gallery_modified = false;

            // Remove deleted images if any
            if (!empty($data['gallery_removed_ids'])) {
                $gallery_removed_json = stripslashes($data['gallery_removed_ids']);
                $removed_ids = json_decode($gallery_removed_json, true);

                if (is_array($removed_ids) && count($removed_ids) > 0) {
                    $gallery_ids_array = array_diff($gallery_ids_array, array_map('intval', $removed_ids));
                    $gallery_modified = true;
                }
            }

            // Handle new gallery uploads
            if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                $files = $_FILES['gallery'];
                $file_count = count($files['name']);

                for ($i = 0; $i < $file_count; $i++) {
                    if (empty($files['name'][$i])) {
                        continue;
                    }

                    $_FILES['gallery_single'] = array(
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    );

                    $gallery_img_id = handle_image_upload('gallery_single', $data['listing_id']);
                    if (is_wp_error($gallery_img_id)) {
                        $errorBag['gallery'] = 'Error uploading image: ' . $gallery_img_id->get_error_message();
                        break;
                    } else {
                        $gallery_ids_array[] = (int) $gallery_img_id;
                        $gallery_modified = true;
                    }
                }
            }

            if ($gallery_modified || !empty($gallery_ids_array)) {
                $gallery_ids_array = array_values(array_unique(array_map('intval', $gallery_ids_array)));
                update_field($gallery_field_name, $gallery_ids_array, $data['listing_id']);
            }
            elseif (empty($gallery_ids_array) && $gallery_modified) {
                delete_field($gallery_field_name, $data['listing_id']);
            }

            break;
        case 'facilities':

            $facilities_list = [
                'after_hours',
                'atm_on_site',
                'cash_payment',
                'credit_card',
                'direct_debit',
                'delivery_service',
                'ev_charge_station',
                'free_quotes',
                'in_store_pickup',
                'mobile_service',
                'pensioner_discount'
            ];

            foreach ($facilities_list as $facility) {
                $field_name = 'facilities_' . $facility;
                if (isset($data[$facility]) && !empty($data[$facility])) {
                    update_field($field_name, 1, $data['listing_id']);
                } else {
                    update_field($field_name, 0, $data['listing_id']);
                }
            }

            $selected_facilities = array();
            foreach ($facilities_list as $facility) {
                if (isset($data[$facility]) && !empty($data[$facility])) {
                    $selected_facilities[] = $facility;
                }
            }

            if (!empty($selected_facilities)) {
                update_field('facilities_selected', $selected_facilities, $data['listing_id']);
            } else {
                update_field('facilities_selected', array(), $data['listing_id']);
            }

            break;
        case 'contact':
            // Validate mandatory fields
            if (empty($data['phone'])) {
                $errorBag['phone'] = 'Phone number is required.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_phone', $data['phone']);

            if (empty($data['mobile'])) {
                $errorBag['mobile'] = 'Mobile number is required.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_mobile', $data['mobile']);

            if (empty($data['email'])) {
                $errorBag['email'] = 'Email address is required.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errorBag['email'] = 'Please enter a valid email address.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_email', $data['email']);

            if (empty($data['address'])) {
                $errorBag['address'] = 'Address is required.';
            }
            update_post_meta($data['listing_id'], $data['section'].'_address', $data['address']);

            // Validate and save optional URL fields
            if (!empty($data['website'])) {
                if (!filter_var($data['website'], FILTER_VALIDATE_URL)) {
                    $errorBag['website'] = 'Please enter a valid website URL (e.g., https://www.example.com).';
                }
            }
            update_post_meta($data['listing_id'], $data['section'].'_website', $data['website'] ?? '');

            // Save business hours (no validation required)
            update_post_meta($data['listing_id'], $data['section'].'_business_hours', $data['business_hours'] ?? '');

            // Validate and save ABN (optional, but if provided should be valid format)
            if (!empty($data['abn'])) {
                // Remove spaces and check if it's 11 digits
                $abn_clean = preg_replace('/\s+/', '', $data['abn']);
                if (!preg_match('/^[0-9]{11}$/', $abn_clean)) {
                    $errorBag['abn'] = 'ABN must be 11 digits.';
                }
            }
            update_post_meta($data['listing_id'], $data['section'].'_abn', $data['abn'] ?? '');

            // Validate and save social media links
            if (!empty($data['facebook_link'])) {
                if (!filter_var($data['facebook_link'], FILTER_VALIDATE_URL)) {
                    $errorBag['facebook_link'] = 'Please enter a valid Facebook URL.';
                }
            }
            update_post_meta($data['listing_id'], $data['section'].'_facebook_link', $data['facebook_link'] ?? '');

            if (!empty($data['instagram_link'])) {
                if (!filter_var($data['instagram_link'], FILTER_VALIDATE_URL)) {
                    $errorBag['instagram_link'] = 'Please enter a valid Instagram URL.';
                }
            }
            update_post_meta($data['listing_id'], $data['section'].'_instagram_link', $data['instagram_link'] ?? '');

            if (!empty($data['x_link'])) {
                if (!filter_var($data['x_link'], FILTER_VALIDATE_URL)) {
                    $errorBag['x_link'] = 'Please enter a valid X (Twitter) URL.';
                }
            }
            update_post_meta($data['listing_id'], $data['section'].'_x_link', $data['x_link'] ?? '');

            break;
        case 'submit':
            break;
    }

    if (count($errorBag) > 0) {
        set_transient('form_message_' . get_current_user_id(), array(
            'type' => 'error',
            'section' => $data['section'],
            'message' => $errorBag
        ), 60);
        wp_redirect(add_query_arg(array('section' => $data['section'], 'listing_id' => $data['listing_id']), wp_get_referer()));
    } else {
        set_transient('form_message_' . get_current_user_id(), array(
            'type' => 'success',
            'section' => $data['next_section'],
            'message' => 'Business Listing Saved!'
        ), 60);
        wp_redirect(add_query_arg(array('section' => $data['next_section'], 'listing_id' => $data['listing_id']), wp_get_referer()));
    }

    wp_die();
}

add_action('wp_ajax_get_region_child_terms', 'get_region_child_terms_callback');
add_action('wp_ajax_nopriv_get_region_child_terms', 'get_region_child_terms_callback');
function get_region_child_terms_callback() {

    // check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_region_child_terms_nonce')) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
        wp_die();
    }

    // get the terms
    $term_id = sanitize_text_field( intval($_POST['term_id']));
    $regions = get_terms('region', array('hide_empty' => false, 'parent' => $term_id));

    $return = [];
    foreach ($regions as $key => $region) {
        $return[$key]['value'] = $region->term_id;
        $return[$key]['text'] = $region->name;
    }

    wp_send_json_success( array( 'terms' => $return ) );
    wp_die();
}

function renderTransientSuccessOrError() {
    $message_data = get_transient('form_message_' . get_current_user_id());
    ob_start();
    if ($message_data['type'] == 'success') {
        ?>
        <div class="notification-success">
            <p style="font-weight: bold;padding:0;margin:0;"><?php echo esc_html($message_data['message']); ?></p>
        </div>
        <?php
    } elseif($message_data['type'] == 'error') {
        ?>
        <div class="notification-error">
            <p style="font-weight: bold;">Whoops! We found some errors with your submission, please review and amend.</p>
            <ul>
                <?php foreach ($message_data['message'] as $error_key => $error_message) { ?>
                    <li><?php echo esc_html($error_message); ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php
    }
    $output = ob_get_clean();
    echo $output;
}

function renderErrorFieldMessage($field_name, $section) {
    $message_data = get_transient('form_message_' . get_current_user_id());
    if (isset($message_data) && $message_data['type'] == 'error' && $message_data['section'] == $section && key_exists($field_name, $message_data['message'])) {
        ?>
            <div class="error-notice"><?php echo $message_data['message'][$field_name]; ?></div>
        <?php
    }
    $output = ob_get_clean();
    echo $output;
}

// https://visitstayexplore.staging-sites.com.au/edit-listing/?section=basics&listing_id=116013
function checkUserAuthorize() {
    if (empty($_GET['listing_id'])) {
        wp_die('Error - Listing ID not found.');
    }
    if (empty($_GET['section']) || !in_array($_GET['section'], array('basics', 'branding', 'facilities', 'contact', 'submit'))) {
        wp_die('Error - Section not found.');
    }
    if (get_current_user_id() != get_post_field('post_author', $_GET['listing_id'])) {
        wp_die('You are not authorized to edit this listing.');
    }
}