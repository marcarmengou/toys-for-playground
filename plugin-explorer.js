jQuery(document).ready(function($) {
    function addPlaygroundButton() {
        $('.install-now').each(function() {
            // Evitar añadir múltiples botones Playground al mismo plugin
            if ($(this).prev().hasClass('button') && $(this).prev().text() === 'Playground') {
                return;
            }

            var pluginSlug = $(this).data('slug');
            var playgroundUrl = 'https://playground.wordpress.net/?plugin=' + pluginSlug + '&url=/wp-admin/&mode=seamless';
            var playgroundButton = '<a href="' + playgroundUrl + '" class="button button-primary" target="_blank">Playground</a>';
            $(this).before(playgroundButton);
        });
    }

    // Llama a la función al cargar la página
    addPlaygroundButton();

    // Escucha eventos AJAX y vuelve a llamar a la función
    $(document).ajaxComplete(function() {
        addPlaygroundButton();
    });
});
