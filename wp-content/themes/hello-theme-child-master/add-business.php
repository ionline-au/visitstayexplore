<?php 
/*
Template Name: Add Business
*/
get_header();
?>
<div class="business-breadcrumb">
    <h2>Add Business Listing</h2>
</div>
<div class="edit-business">
    <div class="container">
        <?php 
            echo do_shortcode('[advanced_form form="form_61e631afb4115"]');
        ?>
    </div>
</div>
<?php
get_footer();
?>