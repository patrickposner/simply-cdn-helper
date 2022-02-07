jQuery(document).ready(function( $ ) {
    // Start generation of single.
    $('#generate-single').on('click', function(){

        var single_id = $(this).attr('data-id');
  
        $.ajax({
            type: 'POST',
            url: ssh_ajax.ajax_url,
            data: {'action' : 'apply_single', 'nonce' : ssh_ajax.run_single_nonce, 'single_id' : single_id },
            dataType: 'json',
            success: function(response) {
                if ( response.success ) {
                    window.location.replace( ssh_ajax.redirect_url );
                }
            },
        });		
    });
});