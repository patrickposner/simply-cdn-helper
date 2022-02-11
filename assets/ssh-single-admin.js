jQuery(document).ready(function( $ ) {

    $( '#exclude-search-url' ).click( function() {
		var last_row = $( '.excludable-search-url-row' ).last();

        if ( $( '#excludable-search-url-row-template' ).length > 0 ) {
            var clone_row = $( '#excludable-search-url-row-template' ).clone().removeAttr( 'id' );
        } else {
            $('#excludable-search-url-rows').append('<div class="excludable-search-url-row" id="excludable-search-url-row-template"><input type="text" name="search-excludable[0]" value="" size="40" /><input class="button remove-excludable-search-url-row" type="button" name="remove" value="Remove" /></div>');
            var clone_row = $( '#excludable-search-url-row-template' ).clone().removeAttr( 'id' );
        }

		var timestamp = new Date().getTime();
		var regex = /excludable\[0\]/g;

		clone_row.html( clone_row.html().replace( regex, 'excludable[' + timestamp + ']' ) );
        clone_row.find('input[type=text]').val('');
		clone_row.insertAfter( last_row );
	} );

	$( '#excludable-search-url-rows' ).on( 'click', '.remove-excludable-search-url-row', function() {
		var $row = $( this ).closest( '.excludable-search-url-row' );
		$row.remove();
	} );

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