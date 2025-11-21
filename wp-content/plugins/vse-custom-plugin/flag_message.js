jQuery(document).ready( function() {
    jQuery(".flag_comment").click( function(e) {
        e.preventDefault();
        comment_id = jQuery(this).attr("data-comment_id")
        nonce = jQuery(this).attr("data-nonce")
        console.log(comment_id);
        console.log(nonce);
        console.log(123);

        jQuery.ajax({
            type : 'post',
            dataType : 'json',
            url : flag_message.ajaxUrl,
            data : { action: 'flag_comment', comment_id : comment_id, nonce: nonce },
            success: function(response) {
                if(response.type == "success") {
                    console.log('success');
                } else {
                    console.log('fail');
                }
            }
        })
    })
})