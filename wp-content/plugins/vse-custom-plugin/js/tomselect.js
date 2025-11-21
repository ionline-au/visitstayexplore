jQuery(document).ready(function () {

    // jQuery('.section').hide();

    jQuery('.tab_button').on('click', function () {

        return;

        // // switch the content
        // jQuery('.section').hide();
        // let tab = jQuery(this).data('attr-tab');
        // jQuery('#' + tab + '_section').fadeIn();
        //
        // // change the class name on the tab
        // jQuery('.tab_button').removeClass('tab_container_active').addClass('tab_container');
        // if (jQuery('#' + tab + '_tab').hasClass('tab_container')) {
        //     jQuery('#' + tab + '_tab').removeClass('tab_container').addClass('tab_container_active');
        // }
    });

});

function saveAndNextStep(step_to_save) {

    // submit the form via ajax
    jQuery.ajax({
        type: 'post',
        dataType: 'json',
        url: '/wp-admin/admin-ajax.php',
        data: jQuery('#vse_custom_plugin_form').serialize() + '&action=save_step&step=' + step_to_save,
        success: function (response) {
            if (response.success == true) {
                // move to the next step
                jQuery('.section').hide();
                let next_step = response.data.next_step;
                jQuery('#' + next_step + '_section').fadeIn();
            }
        }
    });

}