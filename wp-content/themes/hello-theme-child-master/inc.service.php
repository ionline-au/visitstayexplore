<?php

$locations = get_taxonomy_hierarchy('region');
$services = get_terms('services', array('hide_empty' => false, 'parent' => 0));
$regions = get_terms('region', array('hide_empty' => false, 'parent' => 0));

// @todo: fix this to make it dynamic based on type
// @todo: fix the search function in the sidebar
// @tod: remove spam users using the fun
include('inc.local.php');

//switch ($type) {
//    case 'asc':
//        // include('inc.asc_desc.php');
//        include('inc.local.php');
//        break;
//    case 'desc':
//        // include('inc.asc_desc.php');
//        include('inc.local.php');
//        break;
//    case 'local':
//        include('inc.local.php');
//        break;
//    default:
//        include('inc.local.php');
//        break;
//}

/**
 * Usage examples:
 *
 * 1. PREVIEW (safe - doesn't delete anything):
 * $preview = preview_spam_customer_removal();
 * print_r($preview);
 *
 * 2. ACTUAL DELETION (DANGEROUS - permanently deletes users):
 * $result = remove_spam_customer_users();
 * print_r($result);
 */

// Example execution for preview (uncomment to use):
// $preview = preview_spam_customer_removal();
// echo '<pre>';
// print_r($preview);
// echo '</pre>';

// Example execution for actual deletion (uncomment ONLY after testing preview):
// $result = remove_spam_customer_users();
// echo '<pre>';
// print_r($result);
// echo '</pre>';


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

// update the service areas name
foreach ($services as $service) {
    if ($service_id == ($service->term_id)) {
        ?>
            <script>
                jQuery( document ).ready(function() {
                    jQuery('.service_name_jquery').html('<?php echo $service->name; ?>');
                });
            </script>
        <?php
    }
}