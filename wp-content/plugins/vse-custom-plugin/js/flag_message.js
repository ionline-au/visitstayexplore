jQuery(document).ready( function() {
    jQuery(".flag_comment").click( function(e) {
        e.preventDefault();
        comment_id = jQuery(this).attr("data-comment_id");
        nonce = jQuery(this).attr("data-nonce");
        console.log(comment_id);
        console.log(nonce);
        console.log(flag_message);

        jQuery.ajax({
            type : 'post',
            dataType : 'json',
            url : flag_message.url,
            data : { action: 'flag_comment', comment_id : comment_id, nonce: nonce },
            success: function(response) {
                if(response.success == true) {
                    jQuery('#flag_comment_message_' + comment_id).show();
                }
            }
        })
    })
})