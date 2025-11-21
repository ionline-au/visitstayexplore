<?php
    /*
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    */


    // loop through the get super global and massage it into something the templates below expect
    foreach ($_GET as $g_key => $g_value) {
        if ($g_value == '') {
            unset($_GET[$g_key]);
        }
    }
    foreach ($_GET as $g_key => $g_value) {
        if (stristr($g_key, 'area_id_')) {
	        unset($_GET[$g_key]);
            $_GET['area_id'] = $g_value;
        }
    }

?>

<?php
    $locations = get_taxonomy_hierarchy('region');
    $services = get_terms('services', array('hide_empty' => false, 'parent' => 0));
    $regions = get_terms('region', array('hide_empty' => false, 'parent' => 0));
?>

<?php
    switch ($_GET['type']) {
        case 'asc':
            ?>
            <!--ordering by asc-->
            <?php include('asc_desc.php'); ?>
            <?php
            break;
        case 'desc':
            ?>
            <!--ordering by desc-->
            <?php include('asc_desc.php'); ?>
            <?php
            break;
        case 'local':
            ?>
            <!--ordering by local-->
            <div class="listings">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-12">
                            <div class="property-listing">
                                <div class="title">
                                    <h2 style="font-size: 2rem;"><span class="service_name_jquery">Local Listings</span> servicing <?php echo get_term(($_GET['area_id']))->name ?></h2>
                                    <div class="sorting">
                                        <label>Sort By</label>
                                        <form method="post" action="https://visitstayexplore.staging-sites.com.au/wp-admin/admin-ajax.php?action=listing_search_form" class="listing_search">
                                            <select class="form-control" name="type">
                                                <option value="local" <?php if ($_GET['type'] == 'local') {echo 'selected';} ?>>Local Listings</option>
                                                <option value="desc" <?php if ($_GET['type'] == 'desc') {echo 'selected';} ?>>Highest Rated</option>
                                                <option value="asc" <?php if ($_GET['type'] == 'asc') {echo 'selected';} ?>>Lowest Rated</option>
                                            </select>
                                            <input type="hidden" name="area_id" value="<?php echo $_GET['area_id']; ?>">
                                            <input type="hidden" name="service_id" value="<?php echo $_GET['service_id']; ?>">
                                            <input type="hidden" name="action" value="listing_search_form">
                                        </form>
                                    </div>
                                </div>
                                <?php
                                /**
                                 * (1) get all local listings
                                 * (2) get the normal listings (highest reviews first)
                                 * (3) merge them
                                 * (4) remove the dupes
                                 * (5) remove the dupes
                                 */

                                // 1
                                $local_listing_args = array(
                                    'numberposts' => -1,
                                    'post_type' => 'listings',
                                    'post_status' => 'publish',
                                    'meta_key' => 'listing_information_group_your_local_area',
                                    'meta_value' => addslashes($_GET['area_id']),
	                                'tax_query' => array(
		                                array(
			                                'taxonomy' => 'services',
			                                'field' => 'service_id',
			                                'terms' => ($_GET['service_id'])
		                                )
	                                )
                                );
                                $local_listings = get_posts($local_listing_args);
                                $local_listings_arr = json_decode(json_encode($local_listings), true);

                                // now order the local listings by the highest review count
                                global $wpdb;
                                foreach ( $local_listings_arr as $k => $item ) {
	                                $comm_query = "SELECT * FROM  wp_rmp_analytics WHERE `post` = '" . $item['ID'] . "'";
	                                $rating = $wpdb->get_results($comm_query);
	                                if (is_object($rating[0])) {
		                                $local_listings_arr[$k]['final_rating'] = $rating[0]->value;
	                                }
                                }
                                // sort $local_listings_arr by final_rating
                                usort($local_listings_arr, function($a, $b) {
                                    return $a['final_rating'] <=> $b['final_rating'];
                                });
                                $local_listings_arr = array_reverse($local_listings_arr);
                                $local_listings_arr = array_slice($local_listings_arr, 0, 10);

                                // 2
                                $args = array(
	                                'numberposts' => -1,
	                                'post_type' => 'listings',
	                                'post_status' => 'publish',
	                                'tax_query' => array(
		                                array(
			                                'taxonomy' => 'region',
			                                'field' => 'term_id',
			                                'terms' => ($_GET['area_id'])
		                                ),
		                                array(
			                                'taxonomy' => 'services',
			                                'field' => 'service_id',
			                                'terms' => ($_GET['service_id'])
		                                ),
	                                )
                                );
                                $listings = get_posts($args);

                                if (count($listings)) {
	                                foreach ($listings as $key => $listing) {
		                                // (2)
		                                $ratings_sum = intval( get_post_meta( $listing->ID, 'rmp_rating_val_sum', true ) );
		                                $vote_count = intval( get_post_meta( $listing->ID, 'rmp_vote_count', true ) );
		                                if( $ratings_sum && $vote_count ) {
			                                $listing->order = round( ( $ratings_sum / $vote_count ), 1 );
		                                } else {
			                                $listing->order = 0;
		                                }
	                                }
                                }

                                $listings = clone (object)array_reverse((array)$listings);
                                $listings_arry = json_decode(json_encode($listings), true);
                                // $listings_arry = array_reverse($listings_arry); // reverse the array $listings_arry - adjusted in https://ionlineptyltd.teamwork.com/desk/tickets/9432804/messages

                                /*
                                dd('local_before:');
                                dd($local_listings_arr);
                                dd('listings_before:');
                                dd($listings_arry);
                                */

                                // now loop through the $listings_arry and remove any duplicates
                                foreach ($local_listings_arr as $local_key => $local_array) {
	                                foreach ($listings_arry as $normal_key => $normal_array) {
		                                if ($normal_array['ID'] == $local_array['ID']) {
                                            unset($listings_arry[$normal_key]);
                                        }
	                                }
                                }


                                /*
                                dd('local_after:');
                                dd($local_listings_arr);
                                dd('listings_after:');
                                dd($listings_arry);
                                exit();
                                // dd($listings_arry); exit();
                                */

                                // 3
                                $combined = array_merge($local_listings_arr, $listings_arry);
                                // dd($combined); exit();

                                // 4
                                if (isset($combined[0]['ID'])) {
	                                foreach ($combined as $key => $to_list) {
                                        $list = get_post($to_list['ID']);
                                        // get the local area name
                                        $local_area_meta = get_post_meta($to_list['ID'], 'listing_information_group_your_local_area');
                                        if (is_numeric($local_area_meta['0'])) {
	                                        $local_region = get_term( $local_area_meta['0'] )->name;
                                        }

		                                $listings_info_group = get_field( 'listing_information_group', $list->ID);
                                        ?>

                                        <div class="property-item">
                                            <div class="details">
                                                <h3>
                                                    <a href="<?php echo get_permalink($list->ID); ?>" style="color:#000;"><?php echo get_post_meta($list->ID, 'listing_information_group_business_name', true); ?></a>
                                                </h3>
                                                <ul>

                                                    <li><i class="fas fa-phone-alt"></i><a style="font-weight: normal;color: #FF8210;" href="tel:<?php echo get_post_meta($list->ID, 'contact_group_contact_phone', true); ?>"><?php echo get_post_meta($list->ID, 'contact_group_contact_phone', true); ?></a></li>

                                                    <?php
                                                        if (get_post_meta($list->ID, 'contact_group_contact_mobile', true)) {
                                                            ?>
                                                                <li><i class="fas fa-phone-alt"></i><a style="font-weight: normal;color: #FF8210;"  href="tel:<?php echo get_post_meta($list->ID, 'contact_group_contact_mobile', true); ?>"><?php echo get_post_meta($list->ID, 'contact_group_contact_mobile', true); ?></a></li>
                                                            <?php
                                                        }
	                                                ?>


	                                                <?php
                                                        if($listings_info_group['additional_listing_view_text_below_number'] != '') {
                                                            ?>
                                                                <li><div><?php echo $listings_info_group['additional_listing_view_text_below_number']; ?></div></li>
                                                            <?php
                                                        }
	                                                ?>


                                                    <!--<li class="files">
                                                        <i class="fas fas fa-envelope"></i><span style="text-decoration: underline;"><?php /*echo get_post_meta($list->ID, 'contact_group_contact_email', true); */?></span>
                                                    </li>-->
                                                    <li>
                                                        <i class="fas fa-map-marker-alt"></i>Local Area: <?php echo $local_region; ?>
                                                    </li>

	                                                <?php
                                                        if($listings_info_group['additional_listing_view_text_below_content'] != '') {
                                                            ?>
                                                                <li><div><?php echo $listings_info_group['additional_listing_view_text_below_content']; ?></div></li>
                                                            <?php
                                                        }
	                                                ?>

                                                </ul>


                                                <div style="padding-top:5px;">
                                                    <style>
                                                        .business_icons_small {
                                                            max-width: 80px !important;
                                                            float: left;
                                                            padding-right: 10px;
                                                        }
                                                    </style>
		                                            <?php
                                                        if ($listings_info_group['include_abicon_abnn_icon'] == 'Yes') {
                                                            ?>
                                                            <img src="https://visitstayexplore.staging-sites.com.au/wp-content/uploads/2022/10/abn.png" title="ABN Listing" class="business_icons_small">
                                                            <?php
                                                        }
                                                        if ($listings_info_group['icon_insured'] == 'Yes') {
                                                            ?>
                                                            <img src="https://visitstayexplore.staging-sites.com.au/wp-content/uploads/2022/10/insured.png" title="Insured Listing" class="business_icons_small">
                                                            <?php
                                                        }
                                                        if ($listings_info_group['icon_licenced'] == 'Yes') {
                                                            ?>
                                                            <img src="https://visitstayexplore.staging-sites.com.au/wp-content/uploads/2022/10/licenced.png" title="Licenced Listing" class="business_icons_small">
                                                            <?php
                                                        }
		                                            ?>
                                                </div>
                                            </div>
                                            <div class="reviews">
	                                            <?php echo do_shortcode('[display_star_rating_and_review_count comment_id="' . $list->ID . '"]'); ?>
                                                <?php /*echo do_shortcode('[ratemypost-result id="' . $list->ID . '"]') */?>
                                                <!--<a href="<?php /*echo get_permalink($list->ID); */?>/#review">Write a Review</a>-->
                                            </div>
                                            <div class="item-logo">
                                                <?php $listing_info = get_field('listing_information_group', $list->ID); ?>
                                                <?php if (isset($listing_info['logo']['sizes']['large'])) { ?><img src="<?php echo $listing_info['logo']['sizes']['large']; ?>"/><?php } ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <h4 class='listingnotfound'>Sorry, there are no local businesses listed in this area yet!<br>
                                        Please come back soon as our directory grows so you can find more local businesses.<br><br>
                                        Do you know a local business that wants to get found online? Ask them to join today by clicking <a href='/subscriptions'>visitstayexplore.staging-sites.com.au/subscriptions</a> <br>and claim our 30-day introductory offer!
                                    </h4>
                                    <?php
                                } ?>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <p style="float: right;font-weight: bold;padding-bottom:10px;:0;margin:0;"><a href="#" onclick="history.back();" style="text-decoration: none;"><< Go Back</a></p>
                            <div style="clear: both;"></div>
                            <div class="listing-form">
                                <!-- 22 -->
                                <form method="get" action="<?php echo site_url(); ?>/property-lists/">
	                                <?php
                                        $type = 'local';
                                        if (isset($_GET['type'])) {
                                            $type = $_GET['type'];
                                        }
	                                ?>
                                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <select class="form-control" name="area_id">
                                            <?php foreach ($locations as $location) { ?>
                                                <optgroup label="<?php echo $location->name ?>" style="font-size: 16px;padding:10px;font-family: 'Work Sans', Sans-serif;">
                                                    <?php if ($_GET['type'] == 'local') { ?><option>Please Select Location</option><?php } ?>
                                                    <?php foreach ($location->children as $location_child) { ?>
                                                        <option value="<?php echo ($location_child->term_id) ?>" <?php echo (($location_child->term_id) == $_GET['area_id']) ? 'selected' : '' ?>><?php echo $location_child->name ?> </option>
                                                    <?php } ?>
                                                </optgroup>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Services</label>
                                        <select class="form-control" name="service_id">
                                            <option>Please Select Service</option>
                                            <?php foreach ($services as $service) { ?>
                                                <option value="<?php echo ($service->term_id) ?>" <?php echo (($service->term_id) == $_GET['service_id']) ? 'selected' : '' ?> ><?php echo $service->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            break;
	    case '':
		    ?>
            <!--ordering by local-->
            <div class="listings">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-12">
                            <div class="property-listing">
                                <div class="title">
                                    <h2 style="font-size: 2rem;"><span class="service_name_jquery">Local Listings</span> servicing <?php echo get_term(($_GET['area_id']))->name ?></h2>
                                    <div class="sorting">
                                        <label>Sort By</label>
                                        <form method="post" action="https://visitstayexplore.staging-sites.com.au/wp-admin/admin-ajax.php?action=listing_search_form" class="listing_search">
                                            <select class="form-control" name="type">
                                                <option value="local" <?php if ($_GET['type'] == 'local') {echo 'selected';} ?>>Local Listings</option>
                                                <option value="desc" <?php if ($_GET['type'] == 'desc') {echo 'selected';} ?>>Highest Rated</option>
                                                <option value="asc" <?php if ($_GET['type'] == 'asc') {echo 'selected';} ?>>Lowest Rated</option>
                                            </select>
                                            <input type="hidden" name="area_id" value="<?php echo $_GET['area_id']; ?>">
                                            <input type="hidden" name="service_id" value="<?php echo $_GET['service_id']; ?>">
                                            <input type="hidden" name="action" value="listing_search_form">
                                        </form>
                                    </div>
                                </div>
							    <?php
							    /**
							     * (1) get all local listings
							     * (2) get the normal listings (highest reviews first)
							     * (3) merge them
							     * (4) remove the dupes
							     * (5) remove the dupes
							     */

							    // 1
							    $local_listing_args = array(
								    'numberposts' => -1,
								    'post_type' => 'listings',
								    'post_status' => 'publish',
								    'meta_key' => 'listing_information_group_your_local_area',
								    'meta_value' => addslashes($_GET['area_id']),
								    'tax_query' => array(
									    array(
										    'taxonomy' => 'services',
										    'field' => 'service_id',
										    'terms' => ($_GET['service_id'])
									    )
								    )
							    );
							    $local_listings = get_posts($local_listing_args);
							    $local_listings_arr = json_decode(json_encode($local_listings), true);

							    // now order the local listings by the highest review count
							    global $wpdb;
							    foreach ( $local_listings_arr as $k => $item ) {
								    $comm_query = "SELECT * FROM  wp_rmp_analytics WHERE `post` = '" . $item['ID'] . "'";
								    $rating = $wpdb->get_results($comm_query);
								    if (is_object($rating[0])) {
									    $local_listings_arr[$k]['final_rating'] = $rating[0]->value;
								    }
							    }
							    // sort $local_listings_arr by final_rating
							    usort($local_listings_arr, function($a, $b) {
								    return $a['final_rating'] <=> $b['final_rating'];
							    });
							    $local_listings_arr = array_reverse($local_listings_arr);
							    $local_listings_arr = array_slice($local_listings_arr, 0, 10);

							    // 2
							    $args = array(
								    'numberposts' => -1,
								    'post_type' => 'listings',
								    'post_status' => 'publish',
								    'tax_query' => array(
									    array(
										    'taxonomy' => 'region',
										    'field' => 'term_id',
										    'terms' => ($_GET['area_id'])
									    ),
									    array(
										    'taxonomy' => 'services',
										    'field' => 'service_id',
										    'terms' => ($_GET['service_id'])
									    ),
								    )
							    );
							    $listings = get_posts($args);

							    if (count($listings)) {
								    foreach ($listings as $key => $listing) {
									    // (2)
									    $ratings_sum = intval( get_post_meta( $listing->ID, 'rmp_rating_val_sum', true ) );
									    $vote_count = intval( get_post_meta( $listing->ID, 'rmp_vote_count', true ) );
									    if( $ratings_sum && $vote_count ) {
										    $listing->order = round( ( $ratings_sum / $vote_count ), 1 );
									    } else {
										    $listing->order = 0;
									    }
								    }
							    }

							    $listings = clone (object)array_reverse((array)$listings);
                                $listings_arry = json_decode(json_encode($listings), true);
                                // $listings_arry = array_reverse($listings_arry); // reverse the array $listings_arry - adjusted in https://ionlineptyltd.teamwork.com/desk/tickets/9432804/messages

                                /*
                                dd('local_before:');
                                dd($local_listings_arr);
                                dd('listings_before:');
                                dd($listings_arry);
                                */

                                // now loop through the $listings_arry and remove any duplicates
                                foreach ($local_listings_arr as $local_key => $local_array) {
	                                foreach ($listings_arry as $normal_key => $normal_array) {
		                                if ($normal_array['ID'] == $local_array['ID']) {
			                                unset($listings_arry[$normal_key]);
		                                }
	                                }
                                }


                                /*
                                dd('local_after:');
                                dd($local_listings_arr);
                                dd('listings_after:');
                                dd($listings_arry);
                                exit();
                                // dd($listings_arry); exit();
                                */

                                // 3
                                $combined = array_merge($local_listings_arr, $listings_arry);
                                // dd($combined); exit();

                                // 4
                                if (isset($combined[0]['ID'])) {
	                                foreach ($combined as $key => $to_list) {
		                                $list = get_post($to_list['ID']);
		                                // get the local area name
		                                $local_area_meta = get_post_meta($to_list['ID'], 'listing_information_group_your_local_area');
		                                if (is_numeric($local_area_meta['0'])) {
			                                $local_region = get_term( $local_area_meta['0'] )->name;
		                                }

		                                $listings_info_group = get_field( 'listing_information_group', $list->ID);
		                                ?>

                                        <div class="property-item">
                                            <div class="details">
                                                <h3>
                                                    <a href="<?php echo get_permalink($list->ID); ?>" style="color:#000;"><?php echo get_post_meta($list->ID, 'listing_information_group_business_name', true); ?></a>
                                                </h3>
                                                <ul>

                                                    <li><i class="fas fa-phone-alt"></i><a style="font-weight: normal;color: #FF8210;" href="tel:<?php echo get_post_meta($list->ID, 'contact_group_contact_phone', true); ?>"><?php echo get_post_meta($list->ID, 'contact_group_contact_phone', true); ?></a></li>

					                                <?php
					                                if (get_post_meta($list->ID, 'contact_group_contact_mobile', true)) {
						                                ?>
                                                        <li><i class="fas fa-phone-alt"></i><a style="font-weight: normal;color: #FF8210;"  href="tel:<?php echo get_post_meta($list->ID, 'contact_group_contact_mobile', true); ?>"><?php echo get_post_meta($list->ID, 'contact_group_contact_mobile', true); ?></a></li>
						                                <?php
					                                }
					                                ?>


					                                <?php
					                                if($listings_info_group['additional_listing_view_text_below_number'] != '') {
						                                ?>
                                                        <li><div><?php echo $listings_info_group['additional_listing_view_text_below_number']; ?></div></li>
						                                <?php
					                                }
					                                ?>


                                                    <!--<li class="files">
                                                        <i class="fas fas fa-envelope"></i><span style="text-decoration: underline;"><?php /*echo get_post_meta($list->ID, 'contact_group_contact_email', true); */?></span>
                                                    </li>-->
                                                    <li>
                                                        <i class="fas fa-map-marker-alt"></i>Local Area: <?php echo $local_region; ?>
                                                    </li>

					                                <?php
					                                if($listings_info_group['additional_listing_view_text_below_content'] != '') {
						                                ?>
                                                        <li><div><?php echo $listings_info_group['additional_listing_view_text_below_content']; ?></div></li>
						                                <?php
					                                }
					                                ?>

                                                </ul>


                                                <div style="padding-top:5px;">
                                                    <style>
                                                        .business_icons_small {
                                                            max-width: 80px !important;
                                                            float: left;
                                                            padding-right: 10px;
                                                        }
                                                    </style>
					                                <?php
					                                if ($listings_info_group['include_abicon_abnn_icon'] == 'Yes') {
						                                ?>
                                                        <img src="https://visitstayexplore.staging-sites.com.au/wp-content/uploads/2022/10/abn.png" title="ABN Listing" class="business_icons_small">
						                                <?php
					                                }
					                                if ($listings_info_group['icon_insured'] == 'Yes') {
						                                ?>
                                                        <img src="https://visitstayexplore.staging-sites.com.au/wp-content/uploads/2022/10/insured.png" title="Insured Listing" class="business_icons_small">
						                                <?php
					                                }
					                                if ($listings_info_group['icon_licenced'] == 'Yes') {
						                                ?>
                                                        <img src="https://visitstayexplore.staging-sites.com.au/wp-content/uploads/2022/10/licenced.png" title="Licenced Listing" class="business_icons_small">
						                                <?php
					                                }
					                                ?>
                                                </div>
                                            </div>
                                            <div class="reviews">
				                                <?php echo do_shortcode('[display_star_rating_and_review_count comment_id="' . $list->ID . '"]'); ?>
				                                <?php /*echo do_shortcode('[ratemypost-result id="' . $list->ID . '"]') */?>
                                                <!--<a href="<?php /*echo get_permalink($list->ID); */?>/#review">Write a Review</a>-->
                                            </div>
                                            <div class="item-logo">
				                                <?php $listing_info = get_field('listing_information_group', $list->ID); ?>
				                                <?php if (isset($listing_info['logo']['sizes']['large'])) { ?><img src="<?php echo $listing_info['logo']['sizes']['large']; ?>"/><?php } ?>
                                            </div>
                                        </div>
		                                <?php
	                                }
                                } else {
	                                ?>
                                    <h4 class='listingnotfound'>Sorry, there are no local businesses listed in this area yet!<br>
                                        Please come back soon as our directory grows so you can find more local businesses.<br><br>
                                        Do you know a local business that wants to get found online? Ask them to join today by clicking <a href='/subscriptions'>visitstayexplore.staging-sites.com.au/subscriptions</a> <br>and claim our 30-day introductory offer!
                                    </h4>
	                                <?php
                                } ?>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <p style="float: right;font-weight: bold;padding-bottom:10px;:0;margin:0;"><a href="#" onclick="history.back();" style="text-decoration: none;"><< Go Back</a></p>
                            <div style="clear: both;"></div>
                            <div class="listing-form">
                                <!-- 22 -->
                                <form method="get" action="<?php echo site_url(); ?>/property-lists/">
								    <?php
								    $type = 'local';
								    if (isset($_GET['type'])) {
									    $type = $_GET['type'];
								    }
								    ?>
                                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <select class="form-control" name="area_id">
										    <?php foreach ($locations as $location) { ?>
                                                <optgroup label="<?php echo $location->name ?>" style="font-size: 16px;padding:10px;font-family: 'Work Sans', Sans-serif;">
												    <?php if ($_GET['type'] == 'local') { ?><option>Please Select Location</option><?php } ?>
												    <?php foreach ($location->children as $location_child) { ?>
                                                        <option value="<?php echo ($location_child->term_id) ?>" <?php echo (($location_child->term_id) == $_GET['area_id']) ? 'selected' : '' ?>><?php echo $location_child->name ?> </option>
												    <?php } ?>
                                                </optgroup>
										    <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Services</label>
                                        <select class="form-control" name="service_id">
                                            <option>Please Select Service</option>
										    <?php foreach ($services as $service) { ?>
                                                <option value="<?php echo ($service->term_id) ?>" <?php echo (($service->term_id) == $_GET['service_id']) ? 'selected' : '' ?> ><?php echo $service->name; ?></option>
										    <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
		    <?php
		    break;
        default:
	        ?>
            <!--ordering by local-->
            <div class="listings">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-12">
                            <div class="property-listing">
                                <div class="title">
                                    <h2 style="font-size: 2rem;"><span class="service_name_jquery">Local Listings</span> servicing <?php echo get_term(($_GET['area_id']))->name ?></h2>
                                    <div class="sorting">
                                        <label>Sort By</label>
                                        <form method="post" action="https://visitstayexplore.staging-sites.com.au/wp-admin/admin-ajax.php?action=listing_search_form" class="listing_search">
                                            <select class="form-control" name="type">
                                                <option value="local" <?php if ($_GET['type'] == 'local') {echo 'selected';} ?>>Local Listings</option>
                                                <option value="desc" <?php if ($_GET['type'] == 'desc') {echo 'selected';} ?>>Highest Rated</option>
                                                <option value="asc" <?php if ($_GET['type'] == 'asc') {echo 'selected';} ?>>Lowest Rated</option>
                                            </select>
                                            <input type="hidden" name="area_id" value="<?php echo $_GET['area_id']; ?>">
                                            <input type="hidden" name="service_id" value="<?php echo $_GET['service_id']; ?>">
                                            <input type="hidden" name="action" value="listing_search_form">
                                        </form>
                                    </div>
                                </div>
						        <?php
						        /**
						         * (1) get all local listings
						         * (2) get the normal listings (highest reviews first)
						         * (3) merge them
						         * (4) remove the dupes
						         * (5) remove the dupes
						         */

						        // 1
						        $local_listing_args = array(
							        'numberposts' => -1,
							        'post_type' => 'listings',
							        'post_status' => 'publish',
							        'meta_key' => 'listing_information_group_your_local_area',
							        'meta_value' => addslashes($_GET['area_id']),
							        'tax_query' => array(
								        array(
									        'taxonomy' => 'services',
									        'field' => 'service_id',
									        'terms' => ($_GET['service_id'])
								        )
							        )
						        );
						        $local_listings = get_posts($local_listing_args);
						        $local_listings_arr = json_decode(json_encode($local_listings), true);

						        // now order the local listings by the highest review count
						        global $wpdb;
						        foreach ( $local_listings_arr as $k => $item ) {
							        $comm_query = "SELECT * FROM  wp_rmp_analytics WHERE `post` = '" . $item['ID'] . "'";
							        $rating = $wpdb->get_results($comm_query);
							        if (is_object($rating[0])) {
								        $local_listings_arr[$k]['final_rating'] = $rating[0]->value;
							        }
						        }
						        // sort $local_listings_arr by final_rating
						        usort($local_listings_arr, function($a, $b) {
							        return $a['final_rating'] <=> $b['final_rating'];
						        });
						        $local_listings_arr = array_reverse($local_listings_arr);
						        $local_listings_arr = array_slice($local_listings_arr, 0, 10);

						        // 2
						        $args = array(
							        'numberposts' => -1,
							        'post_type' => 'listings',
							        'post_status' => 'publish',
							        'tax_query' => array(
								        array(
									        'taxonomy' => 'region',
									        'field' => 'term_id',
									        'terms' => ($_GET['area_id'])
								        ),
								        array(
									        'taxonomy' => 'services',
									        'field' => 'service_id',
									        'terms' => ($_GET['service_id'])
								        ),
							        )
						        );
						        $listings = get_posts($args);

						        if (count($listings)) {
							        foreach ($listings as $key => $listing) {
								        // (2)
								        $ratings_sum = intval( get_post_meta( $listing->ID, 'rmp_rating_val_sum', true ) );
								        $vote_count = intval( get_post_meta( $listing->ID, 'rmp_vote_count', true ) );
								        if( $ratings_sum && $vote_count ) {
									        $listing->order = round( ( $ratings_sum / $vote_count ), 1 );
								        } else {
									        $listing->order = 0;
								        }
							        }
						        }

						        $listings = clone (object)array_reverse((array)$listings);
                                $listings_arry = json_decode(json_encode($listings), true);
                                // $listings_arry = array_reverse($listings_arry); // reverse the array $listings_arry - adjusted in https://ionlineptyltd.teamwork.com/desk/tickets/9432804/messages

                                /*
                                dd('local_before:');
                                dd($local_listings_arr);
                                dd('listings_before:');
                                dd($listings_arry);
                                */

                                // now loop through the $listings_arry and remove any duplicates
                                foreach ($local_listings_arr as $local_key => $local_array) {
	                                foreach ($listings_arry as $normal_key => $normal_array) {
		                                if ($normal_array['ID'] == $local_array['ID']) {
			                                unset($listings_arry[$normal_key]);
		                                }
	                                }
                                }


                                /*
                                dd('local_after:');
                                dd($local_listings_arr);
                                dd('listings_after:');
                                dd($listings_arry);
                                exit();
                                // dd($listings_arry); exit();
                                */

                                // 3
                                $combined = array_merge($local_listings_arr, $listings_arry);
                                // dd($combined); exit();

                                // 4
                                if (isset($combined[0]['ID'])) {
	                                foreach ($combined as $key => $to_list) {
		                                $list = get_post($to_list['ID']);
		                                // get the local area name
		                                $local_area_meta = get_post_meta($to_list['ID'], 'listing_information_group_your_local_area');
		                                if (is_numeric($local_area_meta['0'])) {
			                                $local_region = get_term( $local_area_meta['0'] )->name;
		                                }

		                                $listings_info_group = get_field( 'listing_information_group', $list->ID);
		                                ?>

                                        <div class="property-item">
                                            <div class="details">
                                                <h3>
                                                    <a href="<?php echo get_permalink($list->ID); ?>" style="color:#000;"><?php echo get_post_meta($list->ID, 'listing_information_group_business_name', true); ?></a>
                                                </h3>
                                                <ul>

                                                    <li><i class="fas fa-phone-alt"></i><a style="font-weight: normal;color: #FF8210;" href="tel:<?php echo get_post_meta($list->ID, 'contact_group_contact_phone', true); ?>"><?php echo get_post_meta($list->ID, 'contact_group_contact_phone', true); ?></a></li>

					                                <?php
					                                if (get_post_meta($list->ID, 'contact_group_contact_mobile', true)) {
						                                ?>
                                                        <li><i class="fas fa-phone-alt"></i><a style="font-weight: normal;color: #FF8210;"  href="tel:<?php echo get_post_meta($list->ID, 'contact_group_contact_mobile', true); ?>"><?php echo get_post_meta($list->ID, 'contact_group_contact_mobile', true); ?></a></li>
						                                <?php
					                                }
					                                ?>


					                                <?php
					                                if($listings_info_group['additional_listing_view_text_below_number'] != '') {
						                                ?>
                                                        <li><div><?php echo $listings_info_group['additional_listing_view_text_below_number']; ?></div></li>
						                                <?php
					                                }
					                                ?>


                                                    <!--<li class="files">
                                                        <i class="fas fas fa-envelope"></i><span style="text-decoration: underline;"><?php /*echo get_post_meta($list->ID, 'contact_group_contact_email', true); */?></span>
                                                    </li>-->
                                                    <li>
                                                        <i class="fas fa-map-marker-alt"></i>Local Area: <?php echo $local_region; ?>
                                                    </li>

					                                <?php
					                                if($listings_info_group['additional_listing_view_text_below_content'] != '') {
						                                ?>
                                                        <li><div><?php echo $listings_info_group['additional_listing_view_text_below_content']; ?></div></li>
						                                <?php
					                                }
					                                ?>

                                                </ul>


                                                <div style="padding-top:5px;">
                                                    <style>
                                                        .business_icons_small {
                                                            max-width: 80px !important;
                                                            float: left;
                                                            padding-right: 10px;
                                                        }
                                                    </style>
					                                <?php
					                                if ($listings_info_group['include_abicon_abnn_icon'] == 'Yes') {
						                                ?>
                                                        <img src="https://visitstayexplore.staging-sites.com.au/wp-content/uploads/2022/10/abn.png" title="ABN Listing" class="business_icons_small">
						                                <?php
					                                }
					                                if ($listings_info_group['icon_insured'] == 'Yes') {
						                                ?>
                                                        <img src="https://visitstayexplore.staging-sites.com.au/wp-content/uploads/2022/10/insured.png" title="Insured Listing" class="business_icons_small">
						                                <?php
					                                }
					                                if ($listings_info_group['icon_licenced'] == 'Yes') {
						                                ?>
                                                        <img src="https://visitstayexplore.staging-sites.com.au/wp-content/uploads/2022/10/licenced.png" title="Licenced Listing" class="business_icons_small">
						                                <?php
					                                }
					                                ?>
                                                </div>
                                            </div>
                                            <div class="reviews">
				                                <?php echo do_shortcode('[display_star_rating_and_review_count comment_id="' . $list->ID . '"]'); ?>
				                                <?php /*echo do_shortcode('[ratemypost-result id="' . $list->ID . '"]') */?>
                                                <!--<a href="<?php /*echo get_permalink($list->ID); */?>/#review">Write a Review</a>-->
                                            </div>
                                            <div class="item-logo">
				                                <?php $listing_info = get_field('listing_information_group', $list->ID); ?>
				                                <?php if (isset($listing_info['logo']['sizes']['large'])) { ?><img src="<?php echo $listing_info['logo']['sizes']['large']; ?>"/><?php } ?>
                                            </div>
                                        </div>
		                                <?php
	                                }
                                } else {
	                                ?>
                                    <h4 class='listingnotfound'>Sorry, there are no local businesses listed in this area yet!<br>
                                        Please come back soon as our directory grows so you can find more local businesses.<br><br>
                                        Do you know a local business that wants to get found online? Ask them to join today by clicking <a href='/subscriptions'>visitstayexplore.staging-sites.com.au/subscriptions</a> <br>and claim our 30-day introductory offer!
                                    </h4>
	                                <?php
                                } ?>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <p style="float: right;font-weight: bold;padding-bottom:10px;:0;margin:0;"><a href="#" onclick="history.back();" style="text-decoration: none;"><< Go Back</a></p>
                            <div style="clear: both;"></div>
                            <div class="listing-form">
                                <!-- 22 -->
                                <form method="get" action="<?php echo site_url(); ?>/property-lists/">
							        <?php
							        $type = 'local';
							        if (isset($_GET['type'])) {
								        $type = $_GET['type'];
							        }
							        ?>
                                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <select class="form-control" name="area_id">
									        <?php foreach ($locations as $location) { ?>
                                                <optgroup label="<?php echo $location->name ?>" style="font-size: 16px;padding:10px;font-family: 'Work Sans', Sans-serif;">
											        <?php if ($_GET['type'] == 'local') { ?><option>Please Select Location</option><?php } ?>
											        <?php foreach ($location->children as $location_child) { ?>
                                                        <option value="<?php echo ($location_child->term_id) ?>" <?php echo (($location_child->term_id) == $_GET['area_id']) ? 'selected' : '' ?>><?php echo $location_child->name ?> </option>
											        <?php } ?>
                                                </optgroup>
									        <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Services</label>
                                        <select class="form-control" name="service_id">
                                            <option>Please Select Service</option>
									        <?php foreach ($services as $service) { ?>
                                                <option value="<?php echo ($service->term_id) ?>" <?php echo (($service->term_id) == $_GET['service_id']) ? 'selected' : '' ?> ><?php echo $service->name; ?></option>
									        <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
?>

<?php
    echo do_shortcode('[elementor-template id="1384"]');

    function get_taxonomy_hierarchy($taxonomy, $parent = 0)
    {
        $taxonomy = is_array($taxonomy) ? array_shift($taxonomy) : $taxonomy;
        $terms = get_terms($taxonomy, array('parent' => $parent, 'hide_empty' => false));
        $children = array();
        foreach ($terms as $term) {
            $term->children = get_taxonomy_hierarchy($taxonomy, $term->term_id);
            $children[] = $term;
        }
        return $children;
    }

    // update the service srea name
    foreach ($services as $service) {
        if ($_GET['service_id'] != '') {
            if ($_GET['service_id'] == ($service->term_id)) {
                ?>
                    <script>
                        jQuery( document ).ready(function() {
                            jQuery('.service_name_jquery').html('<?php echo $service->name; ?>');
                        });
                    </script>
                <?php
            }
        }
    }
?>