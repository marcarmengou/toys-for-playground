jQuery(document).ready(function($) {
    function addPlaygroundButton() {
        $('.install-now').each(function() {
            // Avoid adding multiple Playground buttons to the same plugin
            if ($(this).prev().hasClass('button') && $(this).prev().text() === 'Playground') {
                return;
            }

            var pluginSlug = $(this).data('slug');
            var playgroundUrl = 'https://playground.wordpress.net/?plugin=' + pluginSlug + '&url=/wp-admin/&mode=seamless';
            var playgroundButton = '<a href="' + playgroundUrl + '" class="button button-primary" target="_blank">Playground</a>';
            $(this).before(playgroundButton);
        });
    }

    // Calls the function on page load
    addPlaygroundButton();

    // Listen for AJAX events and call the function again.
    $(document).ajaxComplete(function() {
        addPlaygroundButton();
    });
});
