// Took it from: https://stackoverflow.com/questions/62105561/adding-button-to-header-of-gutenberg-editor-in-wordpress-through-plugin
(function (window, wp) {
    var link_id = 'ssh-trigger-static-export';
    var link_html = '<a id="' + link_id + '" class="components-button is-secondary" href="#">' + ssht.button_label + '</a><span class="spinner"></span>';

    // check if gutenberg's editor root element is present.
    var editorEl = document.getElementById('editor');
    if (!editorEl) { // do nothing if there's no gutenberg root element on page.
        return;
    }

    var unsubscribe = wp.data.subscribe(function () {
        setTimeout(function () {
            if (!document.getElementById(link_id)) {
                var toolbalEl = editorEl.querySelector('.edit-post-header__toolbar');
                if (toolbalEl instanceof HTMLElement) {
                    // Check if post was created.
                    var publish_button = document.getElementsByClassName('editor-post-publish-button__button');

                    if (ssht.publish_button_label !== publish_button[0].textContent) {
                        toolbalEl.insertAdjacentHTML('beforeend', link_html);
                    }
                }
            } else {
                document.getElementById(link_id).onclick = function () {
                    document.getElementById('generate-single').click();
                };
            }

        }, 1)
    });
})(window, wp)