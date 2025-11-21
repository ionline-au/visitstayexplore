<?php
    $listing_info = get_field( 'listing_information_group', $listing_id );

    get_header();

    $geo = file_get_contents( 'https://maps.googleapis.com/maps/api/geocode/json?key=______&address=' . urlencode( get_post_meta( $listing_id, 'contact_group_address', true ) ) . '&sensor=false' );
    $geo = json_decode( $geo, true );
    if ( $geo['status'] = 'OK' ) {
        $latitude = $geo['results'][0]['geometry']['location']['lat'];
        $longitude = $geo['results'][0]['geometry']['location']['lng'];
    }
?>
    <script>
        function initMap() {
            const uluru = {lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?> };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: uluru,
            });
            const marker = new google.maps.Marker({
                position: uluru,
                map: map,
            });
        }
    </script>
    <div class="site-main single-listings" role="main">
		<?php if ( isset( $listing_info['hero_banner_image']['sizes']['2048x2048'] ) ) { ?>
            <div class="top-image" style="height:25vh;background-image: url('<?php echo $listing_info['hero_banner_image']['sizes']['2048x2048']; ?>');background-size:cover;background-repeat: no-repeat;background-position: center center;"></div>
		<?php } ?>
        <div class="slider-bottom">
            <div class="container">
                <div class="property-details">
                    <div class="property-logo" style="margin-right: 30px;"><?php if ( isset( $listing_info['logo']['sizes']['2048x2048'] ) ) { ?><img src="<?php echo $listing_info['logo']['sizes']['2048x2048']; ?>" style="width: 260px; "/><?php } ?></div>
                    <div class="property-info">
                        <h3 style="padding-top:15px;"><?php echo get_post_meta( $listing_id, 'listing_information_group_business_name', true ); ?></h3>
						<?php
                            $region = wp_get_post_terms( $post->ID, 'region', array(
                                'hide_empty' => false,
                                'parent'     => 0
                            ) );
                        ?>
						<?php
                            $services = wp_get_post_terms( $post->ID, 'services', array(
                                'hide_empty' => false,
                                'parent'     => 0
                            ) );
                        ?>
						<?php
                            // get the local area name
                            $local_area_meta = get_post_meta( $post->ID, 'listing_information_group_your_local_area' );
                            if ( is_numeric( $local_area_meta['0'] ) ) {
                                $local_region = get_term( $local_area_meta['0'] )->name;
                            }
						?>
                        <div>
                            <span style="color:#FF8210;"><i class="fas fa-map-marker-alt"></i>&nbsp;&nbsp;Local Area: <?php echo $local_region; ?>
                            </span>
                        </div>
                        <div>
                            <span style="color:#FF8210;"><i class="fas fa-folder-open"></i>&nbsp;
								<?php
                                    if ( is_array( $services ) ) {
                                        $string = '';
                                        foreach ( $services as $serv ) {
                                            $string .= '' . $serv->name . ', ';
                                        }
                                        echo substr_replace( $string, '', - 2 );
                                    }
								?>
                            </span>
                        </div>
                        <div style="padding-top:5px;">
                            <style>
                                .business_icons {
                                    max-width: 80px;
                                    float: left;
                                    padding-right: 10px;
                                }
                            </style>
	                        <?php
                                $listings_info_group = get_field( 'listing_information_group', $listing_id );
                                if ($listings_info_group['include_abicon_abnn_icon'] == 'Yes') {
                                    ?>
                                        <img src="https:///wp-content/uploads/2022/10/abn.png" title="ABN Listing" class="business_icons">
                                    <?php
                                }
                                if ($listings_info_group['icon_insured'] == 'Yes') {
                                    ?>
                                        <img src="/wp-content/uploads/2022/10/insured.png" title="Insured Listing" class="business_icons">
                                    <?php
                                }
                                if ($listings_info_group['icon_licenced'] == 'Yes') {
                                    ?>
                                        <img src="/wp-content/uploads/2022/10/licenced.png" title="Licenced Listing" class="business_icons">
                                    <?php
                                }
	                        ?>
                        </div>

                    </div>
                    <div class="review-section">
                        <?php echo do_shortcode('[display_star_rating_and_review_count comment_id="' . $post->ID . '"]')?>
                    </div>
                    <div class="mobile-section">
                        <!-- <h4><i class="fas fa-mobile-alt"></i><a href="<?php //echo get_permalink( $listing_id ); ?>" class="send-mobile" style="color:#000">Send to Mobile</a></h4> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container">
                <div class="center-content">
                    <div class="row">
                        <div class="col-md-8 col-12">
                            <div class="left-content">
                                <div style="padding-bottom: 15px;"><?php echo nl2br(get_post_meta( $listing_id, 'listing_information_group_introduction', true )); ?></div>
								<?php
                                    $found = false;
                                    for ( $i = 1; $i <= 10; $i ++ ) {
                                        if ( isset( $listing_info[ 'gallery_image_' . $i ]['sizes']['2048x2048'] ) ) {
                                            $found = true;
                                        }
                                    }
								?>
								<?php if ( $found ) { ?>
                                    <div class="contact-info" style="padding-bottom:15px;">
                                        <div class="cycle-slideshow" data-cycle-fx=scrollHorz data-cycle-timeout=0 data-cycle-pager="#adv-custom-pager" data-cycle-pager-template="<a href='#'><img src='{{src}}' width=100 height=70></a>">
											<?php
                                                // dd($listing_info);
                                                for ( $i = 1; $i <= 10; $i ++ ) {
                                                    if ( isset( $listing_info[ 'gallery_image_' . $i ]['sizes']['2048x2048'] ) ) {
                                                        ?>
                                                        <img src="<?php echo $listing_info[ 'gallery_image_' . $i ]['sizes']['2048x2048']; ?>" alt="Business Listing Image for <?php echo get_post_meta( $listing_id, 'listing_information_group_business_name', true ); ?>" style="width: 100%;"/>
                                                        <?php
                                                    }
                                                }
											?>
                                        </div>
                                        <div id="adv-custom-pager" class="center external" style="margin-top:15px;"></div>
                                    </div>
								<?php } ?>
                                <div id="reviews" style="margin-top:150px;" class="hide_for_mobile">
									<?php comments_template(); ?><?php echo do_shortcode( '[ionline_star_reviews post_id=' . $post->ID . ']' ); ?><?php // echo do_shortcode('[ratemypost id="' . $listing_id . '"]');  ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <p style="float: right;font-weight: bold;padding-bottom:10px;:0;margin:0;" class="hide_for_mobile">
                                <a href="#" onclick="history.back();" style="text-decoration: none;"><< Go Back</a>
                            </p>
                            <div style="clear: both;"></div>
                            <div class="right-content">
                                <div class="contact-info">
                                    <h3>Contact Info</h3>
                                    <ul>
										<?php if ( get_post_meta( $listing_id, 'contact_group_contact_phone', true ) ) { ?>
                                            <li>
                                                <i class="fas fa-phone-alt"></i>
                                                <a style="font-weight: normal;color: #FF8210;"  href="tel:<?php echo get_post_meta( $listing_id, 'contact_group_contact_phone', true ); ?>" style="text-decoration: none;">
													<?php echo get_post_meta( $listing_id, 'contact_group_contact_phone', true ); ?>
                                                </a>
                                            </li>
										<?php } ?>
	                                    <?php if ( get_post_meta( $listing_id, 'contact_group_contact_mobile', true ) ) { ?>
                                            <li>
                                                <i class="fas fa-phone-alt"></i>
                                                <a  style="font-weight: normal;color: #FF8210;"  href="tel:<?php echo get_post_meta( $listing_id, 'contact_group_contact_mobile', true ); ?>" style="text-decoration: none;">
				                                    <?php echo get_post_meta( $listing_id, 'contact_group_contact_mobile', true ); ?>
                                                </a>
                                            </li>
	                                    <?php } ?>
										<?php if ( get_post_meta( $listing_id, 'contact_group_contact_email', true ) ) { ?>
                                            <li>
                                                <i class="fas fa-envelope"></i>
                                                <a href="mailto:<?php echo get_post_meta( $listing_id, 'contact_group_contact_email', true ); ?>" style="text-decoration: none;">
													<?php echo get_post_meta( $listing_id, 'contact_group_contact_email', true ); ?>
                                                </a>
                                            </li>
										<?php } ?>

										<?php if ( get_post_meta( $listing_id, 'contact_group_address', true ) ) { ?>
                                            <li>
                                                <i class="fas fa-map-marker-alt"></i>
												<?php echo get_post_meta( $listing_id, 'contact_group_address', true ); ?>
                                            </li>
										<?php } ?>

										<?php if ( get_post_meta( $listing_id, 'contact_group_website', true ) ) { ?>
                                            <li>
                                                <i class="fas fa-globe"></i>
                                                <a href="<?php echo get_post_meta( $listing_id, 'contact_group_website', true ); ?>" target="_blank" style="text-decoration: none;">
													<?php echo get_post_meta( $listing_id, 'contact_group_website', true ); ?>
                                                </a>
                                            </li>
										<?php } ?>
	                                    <?php if ( get_post_meta( $listing_id, 'contact_group_abn', true ) ) { ?>
                                            <li>
                                                <i class="fas fa-book"></i>
	                                            <?php echo get_post_meta( $listing_id, 'contact_group_abn', true ); ?>
                                            </li>
	                                    <?php } ?>

										<?php
										$meta = get_fields( $listing_id );
										// dd($meta['contact_group']['twitter_link']);
										if ( $meta['contact_group']['facebook_link'] != '' ) {
											?>
                                            <li>
                                                <i class="fab fa-facebook-square"></i>
                                                <a href="<?php echo $meta['contact_group']['facebook_link']; ?>" target="_blank" style="text-decoration: none;">
													<?php echo $meta['contact_group']['facebook_link']; ?>
                                                </a>
                                            </li>
											<?php
										}
										?>
										<?php
										if ( $meta['contact_group']['instagram_link'] != '' ) {
											?>
                                            <li>
                                                <i class="fab fa-instagram-square"></i>
                                                <a href="<?php echo $meta['contact_group']['instagram_link']; ?>" target="_blank" style="text-decoration: none;">
													<?php echo $meta['contact_group']['instagram_link']; ?>
                                                </a>
                                            </li>
											<?php
										}
										?>
										<?php
										if ( $meta['contact_group']['twitter_link'] != '' ) {
											?>
                                            <li>
                                                <i class="fab fa-twitter-square"></i>
                                                <a href="<?php echo $meta['contact_group']['twitter_link']; ?>" target="_blank" style="text-decoration: none;">
													<?php echo $meta['contact_group']['twitter_link']; ?>
                                                </a>
                                            </li>
											<?php
										}
										?>
                                    </ul>

                                    <!--<div id="map"></div>-->
                                    <div style="height: 10px;"></div>
                                </div>
								<?php if ( $meta['contact_group']['business_hours'] != '' ) { ?>
                                    <div class="contact-info">
                                        <h3>Business Hours</h3>
                                        <div><?php echo nl2br( $meta['contact_group']['business_hours'] ); ?></div>
                                    </div>
								<?php } ?>

								<?php if ( get_field( 'services_group', $listing_id ) ) { ?>
                                    <?php
                                        $services_info = get_field( 'services_group', $listing_id );
                                        if ( $services_info['services_offered'] != '' ) {
                                            echo '<div class="contact-info"><h3>Services</h3><ul>';
                                            foreach ( $services_info['services_offered'] as $s_key => $s_value ) {
                                                $term = get_term( $s_value, 'listing_business_services' );
                                                echo '<li><i class="fas fa-check"></i>' . $term->name . ' </li>';
                                            }
                                            echo '</ul></div>';
                                        }
                                    ?>
								<?php } ?>

								<?php
								// Display Facilities using ACF fields
								$facilities_list = [
									'after_hours' => 'After Hours Service',
									'atm_on_site' => 'ATM On Site',
									'cash_payment' => 'Cash Payment',
									'credit_card' => 'Credit Card',
									'direct_debit' => 'Direct Debit',
									'delivery_service' => 'Delivery Service',
									'ev_charge_station' => 'EV Charge Station',
									'free_quotes' => 'Free Quotes',
									'in_store_pickup' => 'In Store Pickup',
									'mobile_service' => 'Mobile Service',
									'pensioner_discount' => 'Pensioner Discount'
								];

								$selected_facilities = array();

								// Check each facility field - values are 0 or 1
								foreach ($facilities_list as $facility_key => $facility_label) {
									$field_value = get_field('facilities_' . $facility_key, $listing_id);

									// If value is 1, facility is selected
									if ($field_value == 1) {
										$selected_facilities[$facility_key] = $facility_label;
									}
								}

								// Display facilities if any are selected
								if (!empty($selected_facilities)) {
									echo '<div class="contact-info">';
									echo '<h3>Facilities & Services</h3>';
									echo '<ul style="columns: 2; -webkit-columns: 2; -moz-columns: 2;">';

									foreach ($selected_facilities as $facility_key => $facility_label) {
										echo '<li><i class="fas fa-check"></i>' . $facility_label . '</li>';
									}

									echo '</ul>';
									echo '</div>';
								}
								?>

								<?php
								$listings_info_group = get_field( 'listing_information_group', $listing_id );
								if ( $listings_info_group['licences'] ) {
									echo '<div class="contact-info">';
									echo '<h3>Licences</h3>';
									echo '<div>' . nl2br( $listings_info_group['licences'] ) . '</div>';
									echo '</div>';
								}
								?>
                                <div class="contact-info" style="display: none">
                                    <h3 style="margin-bottom:5px;"><?php echo $post->post_title; ?></h3>
									<?php
									$absolute_url = full_url( $_SERVER );
									// echo str_replace('https://', '', str_replace('http://https://', '', $absolute_url));
									?>
                                    <p style="margin: 0 auto;padding:0;text-align: center;">
                                        <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=<?php echo str_replace( 'http://https://', 'https://', $absolute_url ); ?>&choe=UTF-8">
                                    </p>
                                </div>
                                <div id="reviews" style="margin-top:20px;" class="hide_for_desktop">
									<?php comments_template(); ?><?php echo do_shortcode( '[ionline_star_reviews post_id=' . $post->ID . ']' ); ?><?php // echo do_shortcode('[ratemypost id="' . $listing_id . '"]');  ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php get_sidebar(); ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=____________&callback=initMap&libraries=&v=weekly" async></script>
<?php get_footer(); ?>