jQuery( document ).ready(function() {

    // calculates rent X 2 checking it is a number else alert
    // if all right, calls the calculate total function
    jQuery("#rent2weeks").keyup(function () {
        if ( jQuery("#rent2weeks").val().match(/^\d+$/) ) {
            jQuery("#rent2weekstotal").val(jQuery("#rent2weeks").val() * 2);
            calculate_both_totals();
        } else {
            alert("Only Enter Whole Numbers For Rental Amount");
            return false;
        }}

    );

    // calculates bond X 4 checking it is a number else alert
    // if all right, calls the calculate total function
    jQuery("#bond4weeks").keyup(function () {
        if ( jQuery("#bond4weeks").val().match(/^\d+$/) ) {
            jQuery("#bond4weekstotal").val( jQuery("#bond4weeks").val() * 4 );
            calculate_both_totals();
        } else {
            alert("Only Enter Whole Numbers For Bond Amount");
            return false;
        }}
    );

    // function to check for numbers and then calculate
    function calculate_both_totals() {
        // check numbers
        if ( (jQuery("#rent2weeks").val().match(/^\d+$/)) && (jQuery("#bond4weeks").val().match(/^\d+$/)) ) {

            // checks not empty values
            if ( (jQuery("#rent2weeks").val() != '') && (jQuery("#bond4weeks").val() != '') ) {

                // calculates
                jQuery("#totalmoveincost").val( (jQuery("#rent2weeks").val() * 2) + (jQuery("#bond4weeks").val() * 4));
            }
        }
    }
});