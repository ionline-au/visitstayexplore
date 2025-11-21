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

                                                        // Restore saved local area value (single value now)
                                                        if (window.savedLocalAreaValue && window.savedLocalAreaValue !== "") {
                                                            localEl.tomselect.addItem(window.savedLocalAreaValue, true);
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
                                <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" autocomplete="off" placeholder="Select a service area ...">
                                </select>
                                <?php
                                    // Store saved local area value for restoration after AJAX
                                    $saved_localarea_raw = get_post_meta($listing_id, $section . '_' . $field_name, true);
                                    $localarea_item_string = '';

                                    // Handle single value (can be JSON string or plain string)
                                    if (!empty($saved_localarea_raw)) {
                                        // Try to decode as JSON first
                                        $decoded_localarea = json_decode($saved_localarea_raw);
                                        if (is_array($decoded_localarea) && count($decoded_localarea) > 0) {
                                            // For backwards compatibility with old multi-select data, take the first item
                                            $localarea_item_string = '"' . $decoded_localarea[0] . '"';
                                        } elseif (is_string($decoded_localarea)) {
                                            // JSON-encoded single string
                                            $localarea_item_string = '"' . $decoded_localarea . '"';
                                        } else {
                                            // Plain string format
                                            $localarea_item_string = '"' . $saved_localarea_raw . '"';
                                        }
                                    }
                                ?>
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <script>
                                    // Store saved value for restoration after AJAX loads (single value now)
                                    window.savedLocalAreaValue = <?php echo !empty($localarea_item_string) ? $localarea_item_string : '""'; ?>;

                                    new TomSelect("#<?php echo $field_name; ?>", {
                                        create: true,
                                        maxItems: 1,  // Enforce single selection
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
                                    <div class="ml-1">Select your primary local service area</div>
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
                    <p class="section_subheading">Add your logo, hero banner and gallery.</p>
                    <form id="<?php echo $section; ?>_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">

                        <div class="grid-cols-2 grid gap-4">
                            <div class="col-span-2">
                                <?php $field_name = 'logo'; ?>
                                <label for="<?php echo $field_name; ?>">Logo <span class="mandatory">*</span></label>
                                <br>
                                <?php
                                $logo_id = get_post_meta($listing_id, $section . '_' . $field_name, true);
                                if ($logo_id) {
                                    $logo_url = wp_get_attachment_image_url($logo_id, 'medium');
                                    if ($logo_url) {
                                        echo '<div style="margin-bottom: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">';
                                        echo '<img src="' . esc_url($logo_url) . '" style="max-width: 200px; height: auto; display: block; margin-bottom: 5px;" />';
                                        echo '<small style="color: #666;">Current logo (upload a new one to replace)</small>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                                <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="file" class="file_input" accept="image/jpeg,image/jpg,image/png,image/webp" />
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">PNG/JPG/WebP. Max 5MB.</div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <?php $field_name = 'hero_banner'; ?>
                                <label for="<?php echo $field_name; ?>">Hero Banner <span class="mandatory">*</span></label>
                                <br>
                                <?php
                                $hero_id = get_post_meta($listing_id, $section . '_' . $field_name, true);
                                if ($hero_id) {
                                    $hero_url = wp_get_attachment_image_url($hero_id, 'large');
                                    if ($hero_url) {
                                        echo '<div style="margin-bottom: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">';
                                        echo '<img src="' . esc_url($hero_url) . '" style="max-width: 100%; height: auto; display: block; margin-bottom: 5px;" />';
                                        echo '<small style="color: #666;">Current hero banner (upload a new one to replace)</small>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                                <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="file" class="file_input" accept="image/jpeg,image/jpg,image/png,image/webp" />
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">PNG/JPG/WebP. Max 5MB. Recommended: 1920x600px</div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <?php $field_name = 'gallery'; ?>
                                <label for="<?php echo $field_name; ?>">Photo Gallery</label>
                                <br>
                                <?php
                                // Get gallery from ACF (field name: 'gallery') - returns array format
                                $gallery_images = get_field($section . '_' . $field_name, $listing_id);
                                $gallery_images = is_array($gallery_images) ? $gallery_images : array();

                                if (!empty($gallery_images)) {
                                    echo '<div id="gallery_preview" style="margin-bottom: 15px; padding: 10px; background: #f5f5f5; border-radius: 4px;">';
                                    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">';

                                    foreach ($gallery_images as $gallery_image) {
                                        // Handle both Image ID and Image Array formats
                                        if (is_array($gallery_image)) {
                                            $img_id = $gallery_image['ID'];
                                        } else {
                                            $img_id = $gallery_image;
                                        }

                                        $img_url = wp_get_attachment_image_url($img_id, 'medium');
                                        if ($img_url) {
                                            echo '<div class="gallery-item" style="position: relative; border: 2px solid #ddd; border-radius: 4px; overflow: hidden;">';
                                            echo '<img src="' . esc_url($img_url) . '" style="width: 100%; height: 150px; object-fit: cover; display: block;" />';
                                            echo '<button type="button" class="remove-gallery-image" data-image-id="' . esc_attr($img_id) . '" style="position: absolute; top: 5px; right: 5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-size: 14px; line-height: 1; padding: 0;">×</button>';
                                            echo '</div>';
                                        }
                                    }

                                    echo '</div>';
                                    echo '<small style="color: #666; display: block; margin-top: 10px;">Click × to remove individual photos</small>';
                                    echo '</div>';
                                }
                                ?>
                                <input name="<?php echo $field_name; ?>[]" id="<?php echo $field_name; ?>" type="file" class="file_input" accept="image/jpeg,image/jpg,image/png,image/webp" multiple />
                                <input type="hidden" name="gallery_removed_ids" id="gallery_removed_ids" value="" />
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Select multiple photos. PNG/JPG/WebP. Max 5MB each. (Optional)</div>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const removedIdsInput = document.getElementById('gallery_removed_ids');
                                        const removeButtons = document.querySelectorAll('.remove-gallery-image');
                                        let removedIds = [];

                                        removeButtons.forEach(function(button) {
                                            button.addEventListener('click', function(e) {
                                                e.preventDefault();
                                                const imageId = this.getAttribute('data-image-id');
                                                removedIds.push(imageId);
                                                removedIdsInput.value = JSON.stringify(removedIds);

                                                // Remove the gallery item visually
                                                this.closest('.gallery-item').remove();

                                                // If no more images, remove the preview container
                                                const galleryItems = document.querySelectorAll('.gallery-item');
                                                if (galleryItems.length === 0) {
                                                    const preview = document.getElementById('gallery_preview');
                                                    if (preview) preview.remove();
                                                }
                                            });
                                        });
                                    });
                                </script>
                            </div>
                        </div>

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
                    <p class="section_subheading">Select the facilities and services available at your business.</p>
                    <form id="<?php echo $section; ?>_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">
                        <div class="grid-cols-2 grid gap-4">
                            <div class="col-span-2">
                                <label for="<?php echo $field_name; ?>">Facilities</label>
                                <div style="margin-bottom:15px;">
                                    <?php
                                        $facilties = [
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
                                            'pensioner_discount',
                                        ];

                                        foreach ($facilties as $field_name) {
                                            // Retrieve the ACF field value for this facility
                                            $acf_field_name = 'facilities_' . $field_name;
                                            $is_checked_value = get_field($acf_field_name, $listing_id);

                                            // Check if value is 1 (checked)
                                            $is_checked = ($is_checked_value == 1) ? 'checked' : '';
                                            ?>
                                                <div class="flex justify-start">
                                                    <input type="checkbox" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" style="margin-right:10px;" value="<?php echo ucwords(str_replace('_', ' ', $field_name)); ?>"<?php echo $is_checked; ?>>
                                                    <?php echo ucwords(str_replace('_',' ', $field_name)); ?>
                                                    <?php renderErrorFieldMessage($field_name, $section) ?>
                                                </div>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
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
                    <p class="section_heading">Contact & Details</p>
                    <p class="section_subheading">Provide your contact information and business details.</p>
                    <form id="<?php echo $section; ?>_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">
                        <div class="grid-cols-2 grid gap-4">
                            <div class="col-span-1">
                                <?php $field_name = 'phone'; ?>
                                <label for="<?php echo $field_name; ?>">Phone <span class="mandatory">*</span></label>
                                <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" value="<?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?>" />
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Your main business phone number</div>
                                </div>
                            </div>
                            <div class="col-span-1">
                                <?php $field_name = 'mobile'; ?>
                                <label for="<?php echo $field_name; ?>">Mobile <span class="mandatory">*</span></label>
                                <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" value="<?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?>" />
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Mobile number for customer contact</div>
                                </div>
                            </div>
                            <div class="col-span-1">
                                <?php $field_name = 'email'; ?>
                                <label for="<?php echo $field_name; ?>">Email <span class="mandatory">*</span></label>
                                <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="email" value="<?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?>" />
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Business email address</div>
                                </div>
                            </div>
                            <div class="col-span-1">
                                <?php $field_name = 'website'; ?>
                                <label for="<?php echo $field_name; ?>">Website</label>
                                <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" value="<?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?>" placeholder="https://www.example.com" />
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Your business website (optional)</div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <?php $field_name = 'address'; ?>
                                <label for="<?php echo $field_name; ?>">Address <span class="mandatory">*</span></label>
                                <textarea name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" rows="3"><?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?></textarea>
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center -mt-1!">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Full physical address of your business</div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <?php $field_name = 'business_hours'; ?>
                                <label for="<?php echo $field_name; ?>">Business Hours</label>
                                <textarea name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" rows="5" placeholder="Monday: 9:00 AM - 5:00 PM&#10;Tuesday: 9:00 AM - 5:00 PM&#10;Wednesday: 9:00 AM - 5:00 PM&#10;Thursday: 9:00 AM - 5:00 PM&#10;Friday: 9:00 AM - 5:00 PM&#10;Saturday: 10:00 AM - 2:00 PM&#10;Sunday: Closed"><?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?></textarea>
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center -mt-1!">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">List your operating hours for each day (optional)</div>
                                </div>
                            </div>
                            <div class="col-span-1">
                                <?php $field_name = 'abn'; ?>
                                <label for="<?php echo $field_name; ?>">ABN</label>
                                <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" value="<?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?>" placeholder="12 345 678 901" />
                                <?php renderErrorFieldMessage($field_name, $section) ?>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Australian Business Number (optional)</div>
                                </div>
                            </div>
                            <div class="col-span-1">
                                <!-- Empty column for layout balance -->
                            </div>
                            <div class="col-span-2">
                                <label>Social Media Links</label>
                                <div class="grid-cols-3 grid gap-4">
                                    <div class="col-span-1">
                                        <?php $field_name = 'facebook_link'; ?>
                                        <label for="<?php echo $field_name; ?>" style="font-weight: normal; font-size: 14px;">Facebook</label>
                                        <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" value="<?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?>" placeholder="https://facebook.com/yourpage" />
                                        <?php renderErrorFieldMessage($field_name, $section) ?>
                                    </div>
                                    <div class="col-span-1">
                                        <?php $field_name = 'instagram_link'; ?>
                                        <label for="<?php echo $field_name; ?>" style="font-weight: normal; font-size: 14px;">Instagram</label>
                                        <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" value="<?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?>" placeholder="https://instagram.com/yourpage" />
                                        <?php renderErrorFieldMessage($field_name, $section) ?>
                                    </div>
                                    <div class="col-span-1">
                                        <?php $field_name = 'x_link'; ?>
                                        <label for="<?php echo $field_name; ?>" style="font-weight: normal; font-size: 14px;">X (Twitter)</label>
                                        <input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" value="<?php echo get_post_meta($listing_id, $section . '_' . $field_name, true); ?>" placeholder="https://x.com/yourpage" />
                                        <?php renderErrorFieldMessage($field_name, $section) ?>
                                    </div>
                                </div>
                                <div class="information flex flex-row justify-start! items-center" style="margin-top:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-1">Add links to your social media profiles (optional)</div>
                                </div>
                            </div>
                        </div>

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
                    <p class="section_heading">Review & Submit</p>
                    <p class="section_subheading">Please review your business listing details before submitting.</p>

                    <style>
                        .review-section {
                            background: #f8f9fa;
                            border: 1px solid #e9ecef;
                            border-radius: 8px;
                            padding: 20px;
                            margin-bottom: 20px;
                        }
                        .review-section h3 {
                            color: #333;
                            font-size: 18px;
                            font-weight: 600;
                            margin: 0 0 15px 0;
                            padding-bottom: 10px;
                            border-bottom: 2px solid #e9ecef;
                        }
                        .review-field {
                            margin-bottom: 15px;
                        }
                        .review-field label {
                            display: block;
                            color: #6c757d;
                            font-size: 13px;
                            font-weight: 500;
                            margin-bottom: 5px;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        }
                        .review-field .value {
                            color: #333;
                            font-size: 15px;
                            line-height: 1.6;
                        }
                        .review-field .value.empty {
                            color: #999;
                            font-style: italic;
                        }
                        .review-image {
                            max-width: 150px;
                            height: auto;
                            border-radius: 4px;
                            border: 1px solid #ddd;
                        }
                        .review-gallery {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 10px;
                            margin-top: 10px;
                        }
                        .review-gallery img {
                            width: 100px;
                            height: 100px;
                            object-fit: cover;
                            border-radius: 4px;
                            border: 1px solid #ddd;
                        }
                        .review-facilities {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 10px;
                            margin-top: 5px;
                        }
                        .review-facilities .facility-tag {
                            background: #007cba;
                            color: white;
                            padding: 4px 10px;
                            border-radius: 4px;
                            font-size: 13px;
                        }
                        .review-grid {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 20px;
                        }
                        @media (max-width: 768px) {
                            .review-grid {
                                grid-template-columns: 1fr;
                            }
                        }
                    </style>

                    <?php
                    // Helper function to get term names from IDs
                    function getTermNames($term_ids, $taxonomy) {
                        if (empty($term_ids)) return '';
                        $decoded = json_decode($term_ids);
                        if (!is_array($decoded)) {
                            $decoded = [$term_ids];
                        }
                        $names = [];
                        foreach ($decoded as $term_id) {
                            $term = get_term($term_id, $taxonomy);
                            if ($term && !is_wp_error($term)) {
                                $names[] = $term->name;
                            }
                        }
                        return implode(', ', $names);
                    }

                    // Helper function to get region name
                    function getRegionName($region_id) {
                        if (empty($region_id)) return '';
                        $term = get_term($region_id, 'region');
                        return ($term && !is_wp_error($term)) ? $term->name : '';
                    }
                    ?>

                    <!-- Basic Information Review -->
                    <div class="review-section">
                        <h3>Basic Information</h3>
                        <div class="review-grid">
                            <div class="review-field">
                                <label>Business Name</label>
                                <div class="value <?php echo empty(get_post_meta($listing_id, 'basics_business_name', true)) ? 'empty' : ''; ?>">
                                    <?php echo get_post_meta($listing_id, 'basics_business_name', true) ?: 'Not provided'; ?>
                                </div>
                            </div>
                            <div class="review-field">
                                <label>Region</label>
                                <div class="value <?php echo empty(get_post_meta($listing_id, 'basics_region', true)) ? 'empty' : ''; ?>">
                                    <?php echo getRegionName(get_post_meta($listing_id, 'basics_region', true)) ?: 'Not provided'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="review-field">
                            <label>Introduction</label>
                            <div class="value <?php echo empty(get_post_meta($listing_id, 'basics_introduction', true)) ? 'empty' : ''; ?>">
                                <?php echo get_post_meta($listing_id, 'basics_introduction', true) ?: 'Not provided'; ?>
                            </div>
                        </div>
                        <div class="review-grid">
                            <div class="review-field">
                                <label>Local Service Area</label>
                                <div class="value <?php echo empty(get_post_meta($listing_id, 'basics_localarea', true)) ? 'empty' : ''; ?>">
                                    <?php
                                    $localarea_raw = get_post_meta($listing_id, 'basics_localarea', true);
                                    if (!empty($localarea_raw)) {
                                        $decoded = json_decode($localarea_raw);
                                        if (is_array($decoded) && count($decoded) > 0) {
                                            echo $decoded[0];
                                        } elseif (is_string($decoded)) {
                                            echo $decoded;
                                        } else {
                                            echo $localarea_raw;
                                        }
                                    } else {
                                        echo 'Not provided';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="review-field">
                                <label>Additional Areas</label>
                                <div class="value <?php echo empty(get_post_meta($listing_id, 'basics_additionalareas', true)) ? 'empty' : ''; ?>">
                                    <?php
                                    $areas = json_decode(get_post_meta($listing_id, 'basics_additionalareas', true));
                                    echo !empty($areas) && is_array($areas) ? implode(', ', $areas) : 'Not provided';
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="review-field">
                            <label>Services</label>
                            <div class="value <?php echo empty(get_post_meta($listing_id, 'basics_services', true)) ? 'empty' : ''; ?>">
                                <?php echo getTermNames(get_post_meta($listing_id, 'basics_services', true), 'services') ?: 'Not provided'; ?>
                            </div>
                        </div>
                        <div class="review-field">
                            <label>Licences</label>
                            <div class="value <?php echo empty(get_post_meta($listing_id, 'basics_licences', true)) ? 'empty' : ''; ?>">
                                <?php echo get_post_meta($listing_id, 'basics_licences', true) ?: 'Not provided'; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Branding & Photos Review -->
                    <div class="review-section">
                        <h3>Branding & Photos</h3>
                        <div class="review-grid">
                            <div class="review-field">
                                <label>Logo</label>
                                <?php
                                $logo_id = get_post_meta($listing_id, 'branding_logo', true);
                                if ($logo_id) {
                                    $logo_url = wp_get_attachment_image_url($logo_id, 'thumbnail');
                                    if ($logo_url) {
                                        echo '<img src="' . esc_url($logo_url) . '" class="review-image" alt="Logo" />';
                                    } else {
                                        echo '<div class="value empty">Not provided</div>';
                                    }
                                } else {
                                    echo '<div class="value empty">Not provided</div>';
                                }
                                ?>
                            </div>
                            <div class="review-field">
                                <label>Hero Banner</label>
                                <?php
                                $hero_id = get_post_meta($listing_id, 'branding_hero_banner', true);
                                if ($hero_id) {
                                    $hero_url = wp_get_attachment_image_url($hero_id, 'medium');
                                    if ($hero_url) {
                                        echo '<img src="' . esc_url($hero_url) . '" class="review-image" style="max-width: 300px;" alt="Hero Banner" />';
                                    } else {
                                        echo '<div class="value empty">Not provided</div>';
                                    }
                                } else {
                                    echo '<div class="value empty">Not provided</div>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="review-field">
                            <label>Photo Gallery</label>
                            <?php
                            $gallery_images = get_field('branding_gallery', $listing_id);
                            if (!empty($gallery_images) && is_array($gallery_images)) {
                                echo '<div class="review-gallery">';
                                foreach ($gallery_images as $gallery_image) {
                                    $img_id = is_array($gallery_image) ? $gallery_image['ID'] : $gallery_image;
                                    $img_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                                    if ($img_url) {
                                        echo '<img src="' . esc_url($img_url) . '" alt="Gallery Image" />';
                                    }
                                }
                                echo '</div>';
                            } else {
                                echo '<div class="value empty">No gallery images</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Facilities Review -->
                    <div class="review-section">
                        <h3>Facilities</h3>
                        <div class="review-field">
                            <?php
                            $facilities = [
                                'after_hours' => 'After Hours',
                                'atm_on_site' => 'ATM On Site',
                                'cash_payment' => 'Cash Payment',
                                'credit_card' => 'Credit Card',
                                'direct_debit' => 'Direct Debit',
                                'delivery_service' => 'Delivery Service',
                                'ev_charge_station' => 'EV Charge Station',
                                'free_quotes' => 'Free Quotes',
                                'in_store_pickup' => 'In Store Pickup',
                                'mobile_service' => 'Mobile Service',
                                'pensioner_discount' => 'Pensioner Discount',
                            ];

                            $selected_facilities = [];
                            foreach ($facilities as $field_key => $display_name) {
                                $acf_field_name = 'facilities_' . $field_key;
                                $is_checked = get_field($acf_field_name, $listing_id);
                                if ($is_checked == 1) {
                                    $selected_facilities[] = $display_name;
                                }
                            }

                            if (!empty($selected_facilities)) {
                                echo '<div class="review-facilities">';
                                foreach ($selected_facilities as $facility) {
                                    echo '<span class="facility-tag">' . esc_html($facility) . '</span>';
                                }
                                echo '</div>';
                            } else {
                                echo '<div class="value empty">No facilities selected</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Contact & Details Review -->
                    <div class="review-section">
                        <h3>Contact & Details</h3>
                        <div class="review-grid">
                            <div class="review-field">
                                <label>Phone</label>
                                <div class="value <?php echo empty(get_post_meta($listing_id, 'contact_phone', true)) ? 'empty' : ''; ?>">
                                    <?php echo get_post_meta($listing_id, 'contact_phone', true) ?: 'Not provided'; ?>
                                </div>
                            </div>
                            <div class="review-field">
                                <label>Mobile</label>
                                <div class="value <?php echo empty(get_post_meta($listing_id, 'contact_mobile', true)) ? 'empty' : ''; ?>">
                                    <?php echo get_post_meta($listing_id, 'contact_mobile', true) ?: 'Not provided'; ?>
                                </div>
                            </div>
                            <div class="review-field">
                                <label>Email</label>
                                <div class="value <?php echo empty(get_post_meta($listing_id, 'contact_email', true)) ? 'empty' : ''; ?>">
                                    <?php echo get_post_meta($listing_id, 'contact_email', true) ?: 'Not provided'; ?>
                                </div>
                            </div>
                            <div class="review-field">
                                <label>Website</label>
                                <div class="value <?php echo empty(get_post_meta($listing_id, 'contact_website', true)) ? 'empty' : ''; ?>">
                                    <?php
                                    $website = get_post_meta($listing_id, 'contact_website', true);
                                    echo $website ? '<a href="' . esc_url($website) . '" target="_blank">' . esc_html($website) . '</a>' : 'Not provided';
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="review-field">
                            <label>Address</label>
                            <div class="value <?php echo empty(get_post_meta($listing_id, 'contact_address', true)) ? 'empty' : ''; ?>">
                                <?php echo nl2br(get_post_meta($listing_id, 'contact_address', true)) ?: 'Not provided'; ?>
                            </div>
                        </div>
                        <div class="review-field">
                            <label>Business Hours</label>
                            <div class="value <?php echo empty(get_post_meta($listing_id, 'contact_business_hours', true)) ? 'empty' : ''; ?>">
                                <?php echo nl2br(get_post_meta($listing_id, 'contact_business_hours', true)) ?: 'Not provided'; ?>
                            </div>
                        </div>
                        <div class="review-field">
                            <label>ABN</label>
                            <div class="value <?php echo empty(get_post_meta($listing_id, 'contact_abn', true)) ? 'empty' : ''; ?>">
                                <?php echo get_post_meta($listing_id, 'contact_abn', true) ?: 'Not provided'; ?>
                            </div>
                        </div>
                        <div class="review-field">
                            <label>Social Media</label>
                            <div class="review-grid">
                                <?php
                                $facebook = get_post_meta($listing_id, 'contact_facebook_link', true);
                                $instagram = get_post_meta($listing_id, 'contact_instagram_link', true);
                                $x = get_post_meta($listing_id, 'contact_x_link', true);

                                $social_links = [];
                                if ($facebook) $social_links[] = '<a href="' . esc_url($facebook) . '" target="_blank">Facebook</a>';
                                if ($instagram) $social_links[] = '<a href="' . esc_url($instagram) . '" target="_blank">Instagram</a>';
                                if ($x) $social_links[] = '<a href="' . esc_url($x) . '" target="_blank">X (Twitter)</a>';

                                if (!empty($social_links)) {
                                    echo '<div class="value">' . implode(' | ', $social_links) . '</div>';
                                } else {
                                    echo '<div class="value empty">No social media links provided</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <form id="<?php echo $section; ?>_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" enctype="multipart/form-data">
                        <?php wp_nonce_field('edit_listing_nonce', 'nonce'); ?>
                        <input type="hidden" name="action" value="publish_listing"/>
                        <input type="hidden" name="section" value="<?php echo $section; ?>"/>
                        <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>"/>
                        <input type="hidden" name="next_section" value="finish"/>

                        <div class="flex justify-between items-center" style="margin-top: 30px;">
                            <a href="/edit-listing?section=contact&listing_id=<?php echo $listing_id; ?>" class="button button-primary iol_button"><i aria-hidden="true" class="fas fa-arrow-left"></i> Back</a>
                            <button type="submit" class="button button-primary iol_button" style="background-color: #28a745; border-color: #28a745;">Submit <i aria-hidden="true" class="fas fa-check"></i></button>
                        </div>
                        <div class="clear"></div>
                    </form>
                </div>

                <?php $section = 'finish'; ?>
                <div id="<?php echo $section; ?>_section" class="section" <?php if(isset($_GET['section']) && $_GET['section'] == $section) { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
                    <style>
                        .success-container {
                            text-align: center;
                            padding: 40px 20px;
                            background: #f8f9fa;
                            border-radius: 12px;
                            margin: 20px auto;
                            max-width: 600px;
                        }
                        .success-icon {
                            width: 80px;
                            height: 80px;
                            margin: 0 auto 20px;
                            background-color: #28a745;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            animation: scaleIn 0.5s ease-in-out;
                        }
                        .success-icon svg {
                            width: 40px;
                            height: 40px;
                            color: white;
                        }
                        .success-title {
                            font-size: 28px;
                            font-weight: bold;
                            color: #333;
                            margin-bottom: 15px;
                        }
                        .success-message {
                            font-size: 16px;
                            color: #666;
                            line-height: 1.6;
                            margin-bottom: 30px;
                        }
                        .success-actions {
                            display: flex;
                            gap: 15px;
                            justify-content: center;
                            flex-wrap: wrap;
                        }
                        @keyframes scaleIn {
                            0% {
                                transform: scale(0);
                                opacity: 0;
                            }
                            50% {
                                transform: scale(1.1);
                            }
                            100% {
                                transform: scale(1);
                                opacity: 1;
                            }
                        }
                        @keyframes fadeInUp {
                            0% {
                                opacity: 0;
                                transform: translateY(20px);
                            }
                            100% {
                                opacity: 1;
                                transform: translateY(0);
                            }
                        }
                        .fade-in {
                            animation: fadeInUp 0.6s ease-in-out;
                            animation-delay: 0.3s;
                            animation-fill-mode: both;
                        }
                    </style>

                    <div class="success-container">
                        <div class="success-icon">
                            <!-- You can replace this with your preferred icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <div class="fade-in">
                            <h2 class="success-title">Congratulations!</h2>
                            <p class="success-message">
                                Your business listing has been successfully published and is now live on Visit Stay Explore!<br>
                                Your listing will appear in search results and help customers discover your business.
                            </p>

                            <div class="success-actions">
                                <?php if ($listing_id) : ?>
                                    <a href="<?php echo get_permalink($listing_id); ?>" class="button button-primary iol_button" style="background-color: #28a745; border-color: #28a745;">
                                        <i class="fas fa-eye"></i> View Your Listing
                                    </a>
                                <?php endif; ?>
                                <a href="/my-account/my-listings/" class="button button-primary iol_button">
                                    <i class="fas fa-list"></i> My Listings
                                </a>
                                <a href="/" class="button button-secondary iol_button" style="background: white; color: #333; border: 1px solid #ddd;">
                                    <i class="fas fa-home"></i> Go to Homepage
                                </a>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 40px; padding: 20px; background: #e8f5ff; border-left: 4px solid #007cba; border-radius: 4px;">
                        <h4 style="margin: 0 0 10px 0; color: #007cba;">What's Next?</h4>
                        <ul style="margin: 0; padding-left: 20px; color: #666;">
                            <li>Your listing is now visible to customers searching in your service areas</li>
                            <li>You can edit your listing anytime from your dashboard</li>
                            <li>Consider adding more photos to make your listing stand out</li>
                            <li>Keep your business hours and contact information up to date</li>
                        </ul>
                    </div>
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