<div class="listings">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-12">
				<div class="property-listing">
					<div class="title">
						<h2 style="font-size: 2rem;"><span class="service_name_jquery"></span> Servicing <?php echo get_term(($area_id))->name ?></h2>
						<div class="sorting">
                            <label>Sort By</label>
							<form method="post" action="https://visitstayexplore.staging-sites.com.au/wp-admin/admin-ajax.php?action=listing_search_form" class="listing_search">
								<select class="form-control" name="type">
									<option value="local" <?php if ($type == 'local') {echo 'selected';} ?>>Local Listings</option>
									<option value="desc" <?php if ($type == 'desc') {echo 'selected';} ?>>Highest Rated</option>
									<option value="asc" <?php if ($type == 'asc') {echo 'selected';} ?>>Lowest Rated</option>
								</select>
								<input type="hidden" name="area_id" value="<?php echo $area_id; ?>">
								<input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
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
                     * (6) combine the arrays manually
                     * (7) sort the array by the area_id
					 */

					// 1
					$local_listing_args = array(
						'numberposts' => -1,
						'post_type' => 'listings',
						'post_status' => 'publish',
						'meta_key' => 'listing_information_group_your_local_area',
						'meta_value' => addslashes($area_id),
                        'tax_query' => array(
							array(
								'taxonomy' => 'services',
								'field' => 'service_id',
								'terms' => ($service_id)
							)
						)
					);
					$local_listings = get_posts($local_listing_args);
					$local_listings_arr = json_decode(json_encode($local_listings), true);

                    // get all meta for all the $local_listings_arr
                    foreach ($local_listings_arr as $key => $value) {
                        $local_listings_arr[$key]['meta'] = get_post_meta($value['ID']);
                    }

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
								'terms' => ($area_id)
							),
							array(
								'taxonomy' => 'services',
								'field' => 'service_id',
								'terms' => ($service_id)
							),
						)
					);
					$listings = get_posts($args);

					// get all meta for all the $local_listings_arr
					foreach ($listings as $key => $value) {
						$listings[$key]->meta = get_post_meta($value->ID);
					}
                    // dd($listings); exit();

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

                    // now loop through the $listings_arry and remove any duplicates
                    foreach ($local_listings_arr as $local_key => $local_array) {
                        foreach ($listings_arry as $normal_key => $normal_array) {
                            if ($normal_array['ID'] == $local_array['ID']) {
                                unset($listings_arry[$normal_key]);
                            }
                        }
                    }

                    // (6) create a new array with order of the local listings first
                    $combined = [];
                    foreach($local_listings_arr as $local_listings_only) {
	                    $combined[] = $local_listings_only;
                    }
                    foreach($listings_arry as $normal_listinsg_only) {
                        $combined[] = $normal_listinsg_only;
                    }

                    // (7) recheck the array to make sure that only listings with the area_id are displayed first
                    foreach($combined as $ckey => $cvalue) {
                        if ($cvalue['meta']['listing_information_group_your_local_area'][0] == $area_id) {
                            $first_part[] = $cvalue;
                        } else {
                            $second_part[] = $cvalue;
                        }
                    }
                    $combined = array_merge($first_part, $second_part);

					if (isset($combined[0]['ID'])) {
						foreach ($combined as $key => $single_combined_listing) {

                            $args = array(
                                'p' => $single_combined_listing['ID'],
                                'post_type' => 'listings'
                            );
                            $temp_list = get_posts($args);
                            $list = $temp_list[0];

							$listings_info_group = get_field( 'listing_information_group', $list->ID );

							?>
							<div class="property-item">
								<div class="details">
									<h3>
										<a href="<?php echo get_permalink($list->ID); ?>" style="color:#000;"><?php echo get_post_meta($list->ID, 'listing_information_group_business_name', true); ?></a>
									</h3>
									<ul>
										<li><i class="fas fa-phone-alt"></i><span style="margin-right:20px;"><a style="font-weight: normal;color: #FF8210;"  href="tel:<?php echo get_post_meta($list->ID, 'contact_group_contact_phone', true); ?>"><?php echo get_post_meta($list->ID, 'contact_group_contact_phone', true); ?></a></span></li>
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
                                                <li><div ><?php echo $listings_info_group['additional_listing_view_text_below_number']; ?></div></li>
                                                <?php
                                            }
										?>
										<li>
											<i class="fas fa-map-marker-alt"></i> <?php echo get_post_meta($list->ID, 'contact_group_address', true); ?>
										</li>
										<?php
                                            if($listings_info_group['additional_listing_view_text_below_content'] != '') {
                                                ?>
                                                <li><div ><?php echo $listings_info_group['additional_listing_view_text_below_content']; ?></div></li>
                                                <?php
                                            }
										?>
									</ul>
								</div>
								<div class="reviews">
									<?php echo do_shortcode('[display_star_rating_and_review_count comment_id="' . $list->ID . '"]'); ?>
								</div>
								<div class="item-logo">
									<?php $listing_info = get_field('listing_information_group', $list->ID); // dd($listing_info); ?>
									<?php if (isset($listing_info['logo']['sizes']['large'])) { ?><img src="<?php echo $listing_info['logo']['sizes']['large']; ?>"/><?php } ?>
								</div>
                                <div style="padding-top:5px;">
                                    <style>
                                        .business_icons_small {
                                            max-width: 80px !important;
                                            float: left;
                                            padding-right: 10px;
                                        }
                                    </style>
									<?php
                                        $listings_info_group = get_field( 'listing_information_group', $list->ID );
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
                    <!-- 11 -->
                    <form method="get" action="<?php echo site_url(); ?>/property-lists/">
                        <input type="hidden" name="type" value="<?php echo $type; ?>">
                        <div class="form-group">
                            <label>Location</label>
                            <select class="form-control" name="area_id">
								<?php foreach ($locations as $location) { ?>
                                    <optgroup label="<?php echo $location->name ?>" style="font-size: 16px;padding:10px;font-family: 'Work Sans', Sans-serif;">
										<?php if ($type == 'local') { ?><option>Please Select Location</option><?php } ?>
										<?php foreach ($location->children as $location_child) { ?>
                                            <option value="<?php echo ($location_child->term_id) ?>" <?php echo (($location_child->term_id) == $area_id) ? 'selected' : '' ?>><?php echo $location_child->name ?> </option>
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
                                    <option value="<?php echo ($service->term_id) ?>" <?php echo (($service->term_id) == $service_id) ? 'selected' : '' ?> ><?php echo $service->name; ?></option>
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
    if (!function_exists('sorted_by_order')) {
        function sorted_by_order($a, $b) {
            return strcmp($a->order, $b->order);
        }
    }
?>