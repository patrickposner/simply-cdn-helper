jQuery(document).ready(function( $ ) {

    // Check if the export was an single export.
	const queryString = window.location.search;
	const urlParams = new URLSearchParams(queryString);

    if( 'single_export' === urlParams.get('type') ) {
       $('#generate').hide();
       $('.actions').hide();
    }


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
                    window.location.replace( ssh_ajax.redirect_url + '&type=single_export' );
                }
            },
        });		
    });
});