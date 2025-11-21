<?php
    $regions = get_terms('region', array('hide_empty' => false, 'parent' => 0));

    // foreach region, get the meta called 'region_orders' and order the regions by that
    // get the order meta and apply it to the object
    foreach ($regions as $key => $region) {
        $meta = get_term_meta($region->term_id, 'region_orders');
        $regions[$key]->order = $meta[0];
    }

    // sort the regions by the order
    usort($regions, function ($a, $b) {
        return $a->order - $b->order;
    });

?>
<div class="row">
    <?php foreach ($regions as $region) {
        $thumbnail = get_field('image', $region->taxonomy . '_' . $region->term_id);
        ?>
        <div class="col-md-4 region-homepage-padding">
            <a href="<?php echo site_url(); ?>/service-listings/<?php echo $region->slug; ?>">
                <div class="region-img">
                    <img src="<?php echo $thumbnail['url'] ?>">
                </div>
                <div class="region-title">
                    <h3><?php echo $region->name ?></h3>
                </div>
            </a>
        </div>
    <?php } ?>
</div>