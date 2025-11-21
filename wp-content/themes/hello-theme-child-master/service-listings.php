<?php
/* Template Name: Services Listings */

//global $wp_query;
//echo '<pre>';
//print_r($wp_query->query_vars);
//echo '</pre>';

if (get_query_var('region_slug')) {
    $region_id = get_term_by('slug', get_query_var('region_slug'), 'region')->term_id;
}

if (get_query_var('area_slug')) {
    $area_id = get_term_by('slug', get_query_var('area_slug'), 'region')->term_id;
}

if (get_query_var('service_slug')) {
    $service_id = get_term_by('slug', get_query_var('service_slug'), 'services')->term_id;
}

if (get_query_var('listing_slug')) {
    // get the post not the taxonomy by slug
    $listing_id = get_page_by_path(get_query_var('listing_slug'), OBJECT, 'listings')->ID;
}

$type = $_GET['type'] ?? 'local';

//echo '----region----';
//dd(get_query_var('region_slug'));
//dd($region_id);
//
//echo '----area----';
//dd(get_query_var('area_slug'));
//dd($area_id);
//
//echo '----service----';
//dd(get_query_var('service_slug'));
//dd($service_id);
//
//echo '----listing----';
//dd(get_query_var('listing_slug'));
//dd($listing_id);
//
//echo '----type----';
//dd($type);

get_header();

if ($region_id && !isset($area_id) && !isset($service_id) && !isset($listing_id)) {
    include('inc.region.php');
}

if ($region_id && $area_id && !isset($service_id)&& !isset($listing_id)) {
    include('inc.area.php');
}

if ($region_id && $area_id && $service_id && !isset($listing_id)) {
    include('inc.service.php');
}

if ($region_id && $area_id && $service_id && $listing_id) {
    include('inc.listing.php');
}

get_footer();