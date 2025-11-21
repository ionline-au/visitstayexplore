<?php
get_header();

$region = get_term($region_id, 'region');
$thumbnail = get_field('image', $region->taxonomy . '_' . $region->term_id);

$areas = get_terms('region', array(
    'hide_empty' => 0,
    'parent' => $region->term_id,
));
?>
    <div class="single-region-img">
        <img src="<?php echo $thumbnail['url']; ?>" alt="<?php echo $thumbnail['alt']; ?>" />
    </div>
    <div class="container">
        <div class="regions">
            <div class="region-section">
                <h2>Welcome to the <span><?php echo $region->name; ?></span></h2>
                <?php if (!empty($areas)) { ?>
                    <h4>Please select your area...</h4>
                    <div class="areas-list">
                        <div class="row">
                            <?php foreach ($areas as $area) { ?>
                                <div class="col-lg-4">
                                    <h5>
                                        <a href="<?php echo site_url(); ?>/service-listings/<?php echo ($region->slug); ?>/<?php echo ($area->slug); ?>" style="font-size: 1.3rem !important;"><?php echo $area->name; ?></a>
                                    </h5>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <h4>No area found</h4>
                <?php } ?>
            </div>
        </div>
    </div>
<?php get_footer(); ?>