jQuery(document).ready(function ($) {

    // Repeatable to exclude search terms.
    $('#exclude-search-url').click(function () {
        var last_row = $('.excludable-search-url-row').last();

        if ($('#excludable-search-url-row-template').length > 0) {
            var clone_row = $('#excludable-search-url-row-template').clone().removeAttr('id');
        } else {
            $('#excludable-search-url-rows').append('<div class="excludable-search-url-row" id="excludable-search-url-row-template"><input type="text" name="search-excludable[0]" value="" size="40" /><input class="button remove-excludable-search-url-row" type="button" name="remove" value="Remove" /></div>');
            var clone_row = $('#excludable-search-url-row-template').clone().removeAttr('id');
        }

        var timestamp = new Date().getTime();
        var regex = /excludable\[0\]/g;

        clone_row.html(clone_row.html().replace(regex, 'excludable[' + timestamp + ']'));
        clone_row.find('input[type=text]').val('');
        clone_row.insertAfter(last_row);
    });

    $('#excludable-search-url-rows').on('click', '.remove-excludable-search-url-row', function () {
        var $row = $(this).closest('.excludable-search-url-row');
        $row.remove();
    });

    // Ajax for test e-mail.
    $('#ssh-send-test-email').on('click', function (e) {
        var email = $('#ssh_test_mail').val();

        if ('' !== email) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: ssh_ajax.ajax_url,
                data: { 'action': 'send_test_mail', 'nonce': ssh_ajax.mailer_nonce, 'email': email },
                success: function (response) {
                    if (response.success) {
                        $('<p class="success">' + ssh_ajax.mail_sent + '</p>').insertAfter('#ssh-send-test-email');
                    }
                }
            });
        }
    });

    // Ajax for clear cache
    $('#ssh-clear-cache').on('click', function (e) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: ssh_ajax.ajax_url,
            data: { 'action': 'clear_cache', 'nonce': ssh_ajax.cache_nonce },
            success: function (response) {
                if (response.success) {
                    $('<p class="success">' + ssh_ajax.cache_cleared + '</p>').insertAfter('#ssh-clear-cache');
                }
            }
        });

    });
});