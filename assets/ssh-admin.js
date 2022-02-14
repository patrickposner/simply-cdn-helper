jQuery(document).ready(function( $ ) {
    // Ajax for test e-mail.
    $('#ssh-send-test-email').on('click', function(e) {
        var email = $('#ssh_test_mail').val();

        if( '' !== email ) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: ssh_ajax.ajax_url,
                data: { 'action': 'send_test_mail', 'nonce' : ssh_ajax.mailer_nonce, 'email' : email },
                success: function(response) {
                    if(response.success ) {
                        $( '<p class="success">' + ssh_ajax.mail_sent + '</p>' ).insertAfter( '#ssh-send-test-email' );
                    }
                }
            });
        }
    });

     // Ajax for clear cache
     $('#ssh-clear-cache').on('click', function(e) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: ssh_ajax.ajax_url,
            data: { 'action': 'clear_cache', 'nonce' : ssh_ajax.cache_nonce },
            success: function(response) {
                if(response.success ) {
                    $( '<p class="success">' + ssh_ajax.cache_cleared + '</p>' ).insertAfter( '#ssh-clear-cache' );
                }
            }
        });
       
    });
});