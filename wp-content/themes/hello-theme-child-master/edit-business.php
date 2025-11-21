<?php 
/* Template Name: Edit Business */
    get_header();
?>
<div class="business-breadcrumb">
    <h2>Edit Business Listing</h2>
</div>
<div class="edit-business">
    <div class="container">
        <?php
            echo do_shortcode('[ionline_get_business_listing_header_info post="'.$_GET["id"].'"]');
            echo do_shortcode('[advanced_form form="form_61e631afb4115" redirect="/my-account/my-listings/" filter_mode="1" post="'.$_GET["id"].'"]');
        ?>
    </div>
</div>
<?php
    get_footer();
?>