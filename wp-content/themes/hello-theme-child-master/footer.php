<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('footer')) {
	if (did_action('elementor/loaded') && hello_header_footer_experiment_active()) {
		get_template_part('template-parts/dynamic-footer');
	} else {
		get_template_part('template-parts/footer');
	}
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<?php if (is_user_logged_in()) { ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('.header .elementor-button-link').attr('href', '/add-business/');
        })
    </script>
<?php } ?>

<script>

    jQuery(document).on('click', '.send-mobile', function (e) {
        e.preventDefault();
        var copyText = jQuery(this).attr('href');
        document.addEventListener('copy', function (e) {
            e.clipboardData.setData('text/plain', copyText);
            e.preventDefault();
        }, true);

        document.execCommand('copy');
    })

    jQuery(document).on('change', '.listing_search', function () {
        jQuery(this).submit();
        e.preventDefault();

        var sort = jQuery(this).val();
        if (sort === 'local') {
            var url = "<?php echo site_url();?>/property-lists/?type=local";
        } else {
            // var url = "<?php echo site_url();?>/property-lists/31311?area_id=<?php echo $_GET['area_id']?>&service_id=<?php echo $_GET['service_id']?>&order=" + sort;
            var url = "<?php echo site_url();?>/property-lists/?area_id=<?php echo $_GET['area_id']?>&service_id=<?php echo $_GET['service_id']?>&type=" + local;
        }
        console.log(url);
        location.href = url;
    })

    jQuery(document).ready(function () {
        jQuery('#commentform #submit').prop('disabled', 'true');
    })

    jQuery(document).on('keyup', '#comment', function () {
        var comment = jQuery(this).val();
        if (comment == '') {
            jQuery('#commentform #submit').prop('disabled', 'true');
        } else {
            jQuery('#commentform #submit').removeAttr('disabled');
        }
    })

    // switches out the region dropdown boxes throughout the app
    jQuery(document).on('change', '#region_select', function () {
        var selected_region = jQuery('#region_select option:selected').val();
        if (selected_region === '') {
            selected_region = 0;
        }
        jQuery('.region_selects').hide();
        jQuery('#region_0').hide();
        jQuery('#region_' + selected_region).show();
    })

/*    jQuery(document).on('submit', '#home-page-filter'), function () {
        console.log(123);
        // e.preventDefault();
    }*/

</script>
<script>
    function myRegisterForm() {
        var x = document.getElementById("register");
        var y = document.getElementById("login-form");
        if (x.style.display === "none") {
            x.style.display = "block";
            y.style.display = "none";
        } else {
            x.style.display = "none";
            y.style.display = "block";
        }
    }
</script>
<?php wp_footer(); ?>
</body>
</html>