<?php checkUserAuthorize(); ?>

<?php get_header(); ?>

<?php $listing_id = sanitize_text_field( intval($_GET['listing_id'])); ?>

    <div class="edit_listing_header_bg">
        <div class="container">
            <div class="row">
                <div class="edit_listing_header_text">Add Business Listing</div>
                <div class="edit_listing_subheader_text" id="scroll_to">Fill out your business details below to list your business today.<br>It's quick, easy and will help your business be found online.</div>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top:225px;">
        <div class="row">
            <div class="flex-row" style="margin-top:35px;">
                <?php $section = 'basics'; ?>
                <div id="<?php echo $section; ?>_tab" class="<?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'tab_container_active'; } else { echo 'tab_container'; } ?> tab_button">
                    Step 1: Basics
                </div>
                <?php $section = 'branding'; ?>
                <div id="<?php echo $section; ?>_tab" class="<?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'tab_container_active'; } else { echo 'tab_container'; } ?> tab_button">
                    Step 2: Branding &amp; Photos
                </div>
                <?php $section = 'facilities'; ?>
                <div id="<?php echo $section; ?>_tab" class="<?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'tab_container_active'; } else { echo 'tab_container'; } ?> tab_button">
                    Step 3: Facilities
                </div>
                <?php $section = 'contact'; ?>
                <div id="<?php echo $section; ?>_tab" class="<?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'tab_container_active'; } else { echo 'tab_container'; } ?> tab_button">
                    Step 4: Contact &amp; Details
                </div>
                <?php $section = 'submit'; ?>
                <div id="<?php echo $section; ?>_tab" class="<?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'tab_container_active'; } else { echo 'tab_container'; } ?> tab_button">
                    Step 5: Review &amp; Submit
                </div>
            </div>

            <div class="section_border"  style="margin-bottom:35px;margin-top:20px;">

                <?php renderTransientSuccessOrError(); ?>

                <?php $section = 'basics'; ?>
                <div id="<?php echo $section; ?>_section" class="section" <?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
                    <p class="section_heading">Basics</p>
                    <p class="section_subheading">Tell us who you are and where you operate.</p>
                    <form id="<?php echo $section; ?>_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">
                        <div class="grid-cols-2 grid gap-4">
                            <div class="col-span-1">
                                <?php $field_name = 'business_name'; ?>
                                <label for="<?php echo $field_name; ?>">Business Name <span class="mandatory">*</span></label>
                                <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" value="<?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?>" />
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                            </div>
                            <div class="col-span-1">
                                <?php $field_name = 'region'; ?>
                                <label for="<?php echo $field_name; ?>">Region <span class="mandatory">*</span></label>
                                <?php $regions = get_terms('region', array('hide_empty' => false, 'parent' => 0)); ?>
                                <select name="region" id="<?php echo $field_name; ?>" autocomplete="off" placeholder="Select a region...">
                                    <?php foreach ($regions as $region) : ?>
                                        <option value="<?php echo esc_attr($region->term_id); ?>">
                                            <?php echo esc_html($region->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php  ?>
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <?php $selected_region_value = get_post_meta($listing_id, $section . '_' . $field_name, true); ?>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const loadRegionChildren = function(termId) {
                                            if (!termId) {
                                                return;
                                            }
                                            const data = {
                                                term_id: termId,
                                                action: 'get_region_child_terms',
                                                nonce: '<?php echo wp_create_nonce('get_region_child_terms_nonce'); ?>'
                                            };
                                            jQuery.ajax({
                                                type: 'post',
                                                dataType: 'json',
                                                url: '/wp-admin/admin-ajax.php',
                                                data: data,
                                                success: function (response) {
                                                    const localEl = document.getElementById('localarea');
                                                    if (localEl && localEl.tomselect) {
                                                        localEl.tomselect.clearOptions();
                                                        localEl.tomselect.addOption(response.data.terms);
                                                        localEl.tomselect.refreshOptions(false);

                                                        // Restore saved local area values
                                                        if (window.savedLocalAreaValue && window.savedLocalAreaValue.length > 0) {
                                                            window.savedLocalAreaValue.forEach(function(value) {
                                                                localEl.tomselect.addItem(value, true);
                                                            });
                                                        }
                                                    }

                                                    const areaEl = document.getElementById('additionalareas');
                                                    if (areaEl && areaEl.tomselect) {
                                                        areaEl.tomselect.clearOptions();
                                                        areaEl.tomselect.addOption(response.data.terms);
                                                        areaEl.tomselect.refreshOptions(false);

                                                        // Restore saved additional areas values
                                                        if (window.savedAdditionalAreasValue && window.savedAdditionalAreasValue.length > 0) {
                                                            window.savedAdditionalAreasValue.forEach(function(value) {
                                                                areaEl.tomselect.addItem(value, true);
                                                            });
                                                        }
                                                    }
                                                }
                                            });
                                        };

                                        const initialRegionValue = <?php echo !empty($selected_region_value) ? wp_json_encode($selected_region_value) : 'null'; ?>;

                                        new TomSelect("#<?php echo $field_name; ?>", {
                                            create: true,
                                            sortField: {
                                                field: "text",
                                                direction: "asc"
                                            },
                                            <?php if (!empty($selected_region_value)) { ?>
                                                items: [<?php echo wp_json_encode($selected_region_value); ?>],
                                            <?php } ?>
                                            onInitialize: function() {
                                                if (initialRegionValue) {
                                                    loadRegionChildren(initialRegionValue);
                                                }
                                            },
                                            onChange: function(value) {
                                                loadRegionChildren(value);
                                            },
                                            render: {
                                                option: function (data, escape) {
                                                    return '<div class="iol_tom_option">' + escape(data.text) + '</div>';
                                                },
                                                item: function (data, escape) {
                                                    return '<div class="iol_tom_item">' + escape(data.text) + '</div>';
                                                },
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            <div class="col-span-2">
                                <?php $field_name = 'introduction'; ?>
                                <label for="<?php echo $field_name; ?>">Introduction <span class="mandatory">*</span></label>
                                <textarea name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" rows="5"><?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?></textarea>
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center -mt-1!">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">A short intro shown at the top of your listing (1–2 sentences)</div>
                                </div>

                            </div>
                            <div class="col-span-1">
                                <?php $field_name = 'localarea'; ?>
                                <label for="<?php echo $field_name; ?>">Your Local Service Area <span class="mandatory">*</span></label>
                                <?php $regions = get_terms('region', array('hide_empty' => false, 'parent' => 0)); ?>
                                <select name="<?php echo $field_name; ?>[]" id="<?php echo $field_name; ?>" autocomplete="off" multiple placeholder="Select a service area ...">
                                </select>
                                <?php
                                    // Store saved local area value for restoration after AJAX
                                    $saved_localarea_raw = get_post_meta($listing_id, $section . '_' . $field_name, true);
                                    $localarea_item_string = '';

                                    // Try to decode as JSON first (new format), fallback to string (old format)
                                    $decoded_localarea = json_decode($saved_localarea_raw);
                                    if (is_array($decoded_localarea) && count($decoded_localarea) > 0) {
                                        // New JSON format
                                        foreach ($decoded_localarea as $item) {
                                            $localarea_item_string .= '"' . $item . '",';
                                        }
                                        $localarea_item_string = rtrim($localarea_item_string, ',');
                                    } elseif (!empty($saved_localarea_raw) && !is_array($decoded_localarea)) {
                                        // Old string format (for existing data)
                                        $localarea_item_string = '"' . $saved_localarea_raw . '"';
                                    }
                                ?>
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <script>
                                    // Store saved value for restoration after AJAX loads
                                    window.savedLocalAreaValue = <?php echo !empty($localarea_item_string) ? '[' . $localarea_item_string . ']' : '[]'; ?>;

                                    new TomSelect("#<?php echo $field_name; ?>", {
                                        create: true,
                                        sortField: {
                                            field: "text",
                                            direction: "asc"
                                        },
                                        <?php if (!empty($localarea_item_string)) { ?>
                                            items: [<?php echo $localarea_item_string; ?>],
                                        <?php } ?>
                                        render: {
                                            option: function (data, escape) {
                                                return '<div class="iol_tom_option">' + escape(data.text) + '</div>';
                                            },
                                            item: function (data, escape) {
                                                return '<div class="iol_tom_item">' + escape(data.text) + '</div>';
                                            },
                                        }
                                    });
                                </script>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Tap backspace on your keyboard to remove selections</div>
                                </div>

                            </div>
                            <div class="col-span-1">
                                <?php $field_name = 'additionalareas'; ?>
                                <label for="<?php echo $field_name; ?>">Additional Areas <span class="mandatory">*</span></label>
                                <?php $regions = get_terms('region', array('hide_empty' => false, 'parent' => 0)); ?>
                                <select name="<?php echo $field_name; ?>[]" id="<?php echo $field_name; ?>" autocomplete="off" multiple placeholder="Select a service area ...">
                                </select>
                                <?php
                                    $item_string = '';
                                    $decoded = json_decode(get_post_meta($listing_id, $section . '_' . $field_name, true));
                                    if (is_array($decoded) && count($decoded) > 0) {
                                        foreach ($decoded as $key => $item) {
                                            $item_string .= '"' . $item . '",';
                                        }
                                    }
                                ?>
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <script>
                                    // Store saved value for restoration after AJAX loads
                                    window.savedAdditionalAreasValue = <?php echo !empty($item_string) ? '[' . rtrim($item_string, ',') . ']' : '[]'; ?>;

                                    new TomSelect("#<?php echo $field_name; ?>", {
                                        create: true,
                                        sortField: {
                                            field: "text",
                                            direction: "asc"
                                        },
                                        <?php if (!empty($item_string)) { ?>
                                            items: [<?php echo $item_string; ?>],
                                        <?php } ?>
                                        render: {
                                            option: function (data, escape) {
                                                return '<div class="iol_tom_option">' + escape(data.text) + '</div>';
                                            },
                                            item: function (data, escape) {
                                                return '<div class="iol_tom_item">' + escape(data.text) + '</div>';
                                            },
                                        }
                                    });
                                </script>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Tap backspace on your keyboard to remove selections</div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <?php $field_name = 'services'; ?>
                                <label for="<?php echo $field_name; ?>">Service Selection <span class="mandatory">*</span></label>
                                <?php $regions = get_terms('services', array('hide_empty' => false, 'parent' => 0)); ?>
                                <select name="<?php echo $field_name; ?>[]" id="<?php echo $field_name; ?>" autocomplete="off" multiple>
                                    <option value="">Select a service area ...</option>
                                    <?php foreach ($regions as $region) : ?>
                                        <option value="<?php echo esc_attr($region->term_id); ?>">
                                            <?php echo esc_html($region->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php
                                    $item_string = '';
                                    $decoded = json_decode(get_post_meta($listing_id, $section . '_' . $field_name, true));
                                    if (is_array($decoded) && count($decoded) > 0) {
                                        foreach ($decoded as $key => $item) {
                                            $item_string .= '"' . $item . '",';
                                        }
                                    }
                                ?>
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <script>
                                    new TomSelect("#<?php echo $field_name; ?>", {
                                        create: true,
                                        sortField: {
                                            field: "text",
                                            direction: "asc"
                                        },
                                        <?php if (!empty($item_string)) { ?>
                                            items: [<?php echo $item_string; ?>],
                                        <?php } ?>
                                        render: {
                                            option: function (data, escape) {
                                                return '<div class="iol_tom_option">' + escape(data.text) + '</div>';
                                            },
                                            item: function (data, escape) {
                                                return '<div class="iol_tom_item">' + escape(data.text) + '</div>';
                                            },
                                        }
                                    });
                                </script>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Pick the categories that best describe you (e.g. Op Shop, Cafe, B&B). Tap backspace on your keyboard to remove selections</div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <?php $field_name = 'licences'; ?>
                                <label for="<?php echo $field_name; ?>">Licences <span class="mandatory">*</span></label>
                                <textarea name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" rows="3"><?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?></textarea>
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center -mt-1!">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"  clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">ABN, licences or certifications (optional)</div>
                                </div>
                            </div>
                        </div>

                        <?php wp_nonce_field('edit_listing_nonce', 'nonce'); ?>
                        <input type="hidden" name="action" value="edit_listing"/>
                        <input type="hidden" name="section" value="<?php echo $section; ?>"/>
                        <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>"/>
                        <input type="hidden" name="next_section" value="branding"/>
                        <button type="submit" class="button button-primary iol_button pull-right">Next <i aria-hidden="true" class="fas fa-arrow-right"></i></button>
                        <div class="clear"></div>

                    </form>
                </div>

                <?php $section = 'branding'; ?>
                <div id="<?php echo $section; ?>_section" class="section" <?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
                    <p class="section_heading">Branding</p>
                    <p class="section_subheading">Tell us who you are and where you operate.</p>
                    <form id="<?php echo $section; ?>_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">

                        <?php wp_nonce_field('edit_listing_nonce', 'nonce'); ?>
                        <input type="hidden" name="action" value="edit_listing"/>
                        <input type="hidden" name="section" value="<?php echo $section; ?>"/>
                        <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>"/>
                        <input type="hidden" name="next_section" value="facilities"/>
                        <div class="flex justify-between items-center">
                            <a href="/edit-listing?section=basics&listing_id=<?php echo $listing_id; ?>" class="button button-primary iol_button pull-right"><i aria-hidden="true" class="fas fa-arrow-left"></i> Back</a>
                            <button type="submit" class="button button-primary iol_button pull-right">Next <i aria-hidden="true" class="fas fa-arrow-right"></i></button>
                        </div>
                        <div class="clear"></div>

                    </form>
                </div>

                <?php $section = 'facilities'; ?>
                <div id="<?php echo $section; ?>_section" class="section" <?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
                    <p class="section_heading">Facilities</p>
                    <p class="section_subheading">Tell us who you are and where you operate.</p>
                    <form id="<?php echo $section; ?>_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">

                        <?php wp_nonce_field('edit_listing_nonce', 'nonce'); ?>
                        <input type="hidden" name="action" value="edit_listing"/>
                        <input type="hidden" name="section" value="<?php echo $section; ?>"/>
                        <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>"/>
                        <input type="hidden" name="next_section" value="contact"/>
                        <div class="flex justify-between items-center">
                            <a href="/edit-listing?section=branding&listing_id=<?php echo $listing_id; ?>" class="button button-primary iol_button pull-right"><i aria-hidden="true" class="fas fa-arrow-left"></i> Back</a>
                            <button type="submit" class="button button-primary iol_button pull-right">Next <i aria-hidden="true" class="fas fa-arrow-right"></i></button>
                        </div>
                        <div class="clear"></div>
                    </form>
                </div>

                <?php $section = 'contact'; ?>
                <div id="<?php echo $section; ?>_section" class="section" <?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
                    <p class="section_heading">Contact</p>
                    <p class="section_subheading">Tell us who you are and where you operate.</p>
                    <form id="<?php echo $section; ?>_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">

                        <?php wp_nonce_field('edit_listing_nonce', 'nonce'); ?>
                        <input type="hidden" name="action" value="edit_listing"/>
                        <input type="hidden" name="section" value="<?php echo $section; ?>"/>
                        <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>"/>
                        <input type="hidden" name="next_section" value="submit"/>
                        <div class="flex justify-between items-center">
                            <a href="/edit-listing?section=facilities&listing_id=<?php echo $listing_id; ?>" class="button button-primary iol_button pull-right"><i aria-hidden="true" class="fas fa-arrow-left"></i> Back</a>
                            <button type="submit" class="button button-primary iol_button pull-right">Next <i aria-hidden="true" class="fas fa-arrow-right"></i></button>
                        </div>
                        <div class="clear"></div>

                    </form>
                </div>

                <?php $section = 'submit'; ?>
                <div id="<?php echo $section; ?>_section" class="section" <?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
                    <p class="section_heading">Submit</p>
                    <p class="section_subheading">Tell us who you are and where you operate.</p>
                    <form id="<?php echo $section; ?>_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">

                        <?php wp_nonce_field('edit_listing_nonce', 'nonce'); ?>
                        <input type="hidden" name="action" value="edit_listing"/>
                        <input type="hidden" name="section" value="<?php echo $section; ?>"/>
                        <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>"/>
                        <input type="hidden" name="next_section" value="finish"/>
                        <div class="flex justify-between items-center">
                            <a href="/edit-listing?section=contact&listing_id=<?php echo $listing_id; ?>" class="button button-primary iol_button pull-right"><i aria-hidden="true" class="fas fa-arrow-left"></i> Back</a>
                            <button type="submit" class="button button-primary iol_button pull-right">Next <i aria-hidden="true" class="fas fa-arrow-right"></i></button>
                        </div>
                        <div class="clear"></div>

                    </form>
                </div>

                <?php $section = 'finish'; ?>
                <div id="<?php echo $section; ?>_section" class="section" <?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
                    <p class="section_heading">Finish</p>
                    <p class="section_subheading">Tell us who you are and where you operate.</p>
                </div>

                <?php delete_transient('form_message_' . get_current_user_id()); ?>
            </div>
        </div>
    </div>
    <div style="height: 15px;"></div>

    <?php if ($section != 'basics') { ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const section = document.getElementById('scroll_to');
                if (section) {
                    section.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                        inline: 'nearest'
                    });
                }
            });
        </script>
    <?php } ?>

<?php get_footer(); ?>