jQuery(document).ready(function($) {
    // Function to dismiss banner
    function dismissBanner($banner) {
        $.ajax({
            url: wtDecoratorBFCM.ajaxurl,
            type: 'POST',
            data: {
                action: 'wt_decorator_dismiss_bfcm',
                nonce: wtDecoratorBFCM.nonce
            },
            success: function(response) {
                if (response.success) {
                    $banner.slideUp();
                }
            }
        });
    }

    // Handle dismiss button click using event delegation
    $(document).on('click', '.wt-decorator-bfcm-dismiss', function(e) {
        e.preventDefault();
        var $banner = $(this).closest('.wt-decorator-bfcm-banner');
        dismissBanner($banner);
    });

    // Handle main button click using event delegation
    $(document).on('click', '.wt-decorator-bfcm-button', function(e) {
        var $banner = $(this).closest('.wt-decorator-bfcm-banner');
        // Dismiss banner before redirecting
        dismissBanner($banner);
    });
});
