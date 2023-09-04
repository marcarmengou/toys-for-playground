jQuery(document).ready(function($) {
    // MutationObserver to detect dynamically loaded elements
    function handleMutation(mutationsList, observer) {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                jQuery('.theme').each(function() {
                    var themeSlug = jQuery(this).data('slug');
                    var themeActionsDiv = jQuery(this).find('.theme-actions');
                    // Avoid adding multiple Playground buttons to the same theme
                    if (themeActionsDiv.find('.playground-button').length > 0) {
                        return;
                    }
                    if (themeSlug) {
                        var playgroundUrl = 'https://playground.wordpress.net/?theme=' + themeSlug + '&mode=seamless';
                        // Your logic to add the Playground button here
                        themeActionsDiv.append('<a href="' + playgroundUrl + '" class="button button-primary playground-button" target="_blank">Playground</a>');
                    } else {
                        console.log('Slug is undefined');
                    }
                });
            }
        }
    }

    // Set up the observer
    const targetNode = document.querySelector('.themes');  // Replace '.themes' with the actual parent element class if different
    const config = { childList: true, subtree: true };
    const observer = new MutationObserver(handleMutation);

    // Start observing
    observer.observe(targetNode, config);
    });
