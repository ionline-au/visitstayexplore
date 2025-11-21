jQuery(document).ready(function($) {

    $('#categories').select2({
        placeholder: 'Select categories',
        allowClear: true,
        width: '100%',
        multiple: true,
    });

    // Basic initialization - single select - sample
    $('#iol_sample').select2({
        placeholder: 'Select a category',
        allowClear: true,
        width: '100%'
    });


    // All select elements at once - sample
    $('.iol_sample').select2({
        placeholder: 'Select an option',
        allowClear: true,
        width: '100%'
    });

});