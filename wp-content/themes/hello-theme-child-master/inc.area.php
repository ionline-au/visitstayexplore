<?php

$service = [];
$service_data = [];
$args = array(
    'numberposts' => -1,
    'post_type' => 'listings',
    'fields' => 'ids',
    'tax_query' => array(
        array(
            'taxonomy' => 'region',
            'field' => 'term_id',
            'terms' => $area_id
        )
    )
);
$listing_ids = get_posts($args);

foreach ($listing_ids as $list_id) {
    $service_ids = wp_get_post_terms($list_id, 'services', array('fields' => 'ids'));
    $service = array_unique(array_merge($service, $service_ids));
}

foreach ($service as $key => $value) {
    $string = get_term($value)->name;
    $uniqeCharacter = substr($string, 0, 1);
    $service_data[$key]['id'] = $value;
    $service_data[$key]['name'] = $term_name = get_term($value)->name;
    $service_data[$key]['uniqe_alphabate'] = substr($string, 0, 1);
    $service_data[$key]['term_id'] = $area_id;
    $service_data[$key]['slug'] = sanitize_title($service_data[$key]['name']);
}
uasort($service_data, 'cmp');

$result = array();
foreach ($service_data as $element) {
    $result[$element['uniqe_alphabate']][] = $element;
}

// get the region featured image
if (!empty(get_query_var('region_slug'))) {
    $region = get_terms('region', array(
        'hide_empty' => 0,
        'slug' => get_query_var('region_slug'),
    ));
    if (!empty($region[0])) {
        $meta = get_term_meta(  $region[0]->term_id, 'image' );
        $media = wp_get_attachment_url($meta[0]);
        ?>
        <div class="single-region-img">
            <img src="<?php echo $media; ?>">
        </div>
        <?php
    }
}
?>

<div class="container">
    <div class="services-listings">
        <?php
        // get the meta called banner_image from the category
        $meta = get_term_meta( $area_id, 'banner_image' );

        // get an optional link for each of the images
        $link = [];
        $link[0] = '#';
        $link = get_term_meta( $area_id, 'banner_link' );
        $media = wp_get_attachment_url($meta[0]);
        if (!is_null($media)) {
            ?>
            <p style="text-align: center;padding:0;margin:0;"><a href="<?php echo $link[0]; ?>"><img src="<?php echo $media; ?>" style="margin: 0 auto;"></a></p>
            <?php
        }
        ?>
        <?php if (!empty($result)) { ?>
            <h2 style="text-align: center;">Find your local businesses servicing <span style="color: #FF8210FF;"><?php echo get_term($area_id)->name ?></span>...</h2>
            <?php echo do_shortcode('[elementor-template id="1475"]'); ?>
            <div class="row">
                <?php foreach ($result as $k => $v) { ?>
                    <div class="col-lg-3">
                        <h4 style="color: #FF8210FF;"><?php echo $k; ?></h4>
                        <?php foreach ($v as $service_value) { $serv?>
                            <h5 style="">
                                <!--<a href="<?php echo site_url(); ?>/service-listings/<?php echo get_query_var('region_slug'); ?>/?area_id=<?php echo ($service_value['term_id']) ?>&service_id=<?php echo ($service_value['id']) ?>&type=local"><?php echo $service_value['name'] ?></a>-->
                                <a href="<?php echo site_url(); ?>/service-listings/<?php echo get_query_var('region_slug'); ?>/<?php echo get_query_var('area_slug'); ?>/<?php echo $service_value['slug'] ?>"><?php echo $service_value['name'] ?></a>
                            </h5>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } else {
            echo "
                    <div class='container'>
                        <h4 class='select-area-service' style='padding-bottom:10px;margin-bottom:10px;'>Whoops! No services found in <span style='color: #FF8210;'>" . get_term($area_id)->name . "!</span></h4>
                        <h4 class='listingnotfound' style='text-align: center;font-weight: normal'>Sorry, there are no local businesses listed in this area yet!<br>
                        Please come back soon as our directory grows so you can find more local businesses.<br><br>
                        Do you know a local business that wants to get found online? Ask them to join today by clicking <a href='/subscriptions'>visitstayexplore.staging-sites.com.au/subscriptions</a> and claim our 30-day introductory offer!</h4>
                        
                    </div>";
        } ?>

    </div>
</div>

<?php
    echo do_shortcode('[elementor-template id="1384"]');
    function cmp($a, $b)
    {
        if ($a['name'] == $b['name']) {
            return 0;
        }
        return ($a['name'] < $b['name']) ? -1 : 1;
    }
?>