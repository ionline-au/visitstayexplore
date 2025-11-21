    <?php
        $locations = get_taxonomy_hierarchy('region');
        $services = get_terms('services', array('hide_empty' => false, 'parent' => 0));
        $regions = get_terms('region', array('hide_empty' => false, 'parent' => 0));
    ?>
    <div class="elementor-widget-container" style="padding-bottom:10px;">
        <form class="elementor-form" id="home-page-filter" method="get" action="<?php echo site_url(); ?>/property-lists/">
            <div class="elementor-form-fields-wrapper elementor-labels-above">
                <div class="elementor-field-type-text elementor-field-group elementor-column elementor-field-group-name elementor-col-30">
                    <label>Region</label>

                    <select class="form-control" id="region_select"  >
                        <option value="">Please Select Region</option>
						<?php foreach ($regions as $region) { ?>
                            <option value="<?php echo $region->term_id; ?>"><?php echo $region->name ?> </option>
						<?php } ?>
                    </select>

                </div>
                <div class="elementor-field-type-text elementor-field-group elementor-column elementor-field-group-name elementor-col-30">
                    <label>Location</label>

                    <select class="form-control" id="region_0"  >
                        <option value="none">Please Select Area</option>
                    </select>

					<?php foreach ($regions as $region) { ?>
                        <select class="form-control region_selects" name="area_id_<?php echo $region->term_id; ?>" id="region_<?php echo $region->term_id; ?>" style="display: none;" >
                            <option value="">Please Select Location</option>
							<?php
							    $terms = get_terms('region', array('parent' => $region->term_id, 'hide_empty' => false));
							?>
							<?php foreach ($terms as $term) { ?>
                                <option value="<?php echo ($term->term_id) ?>"><?php echo $term->name ?> </option>
							<?php } ?>
                        </select>
					<?php } ?>

                </div>
                <div class="elementor-field-type-select elementor-field-group elementor-column elementor-field-group-email elementor-col-30 elementor-field-required">
                    <label>Services</label>
                    <select class="form-control" name="service_id" required >
                        <option value="">Please Select Service</option>
						<?php foreach ($services as $service) { ?>
                            <option value="<?php echo ($service->term_id) ?>"><?php echo $service->name; ?></option>
						<?php } ?>
                    </select>
                    <input type="hidden" name="type" value="local">
                </div>
                <div class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-20 e-form__buttons" style="margin-top:10px;">
                    <button type="submit" class="elementor-button elementor-size-md">
                        <span>
                            <span class="elementor-button-icon"></span>
                            <span class="elementor-button-text">SEARCH</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php

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
?>