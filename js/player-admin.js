(function ($) {
    var dialog = $('#add-points');

    dialog.click(function () {
        $('dialog').show();
        $('#wpwrap').before('<div id="dim"></div>');
    });

    $('#add-points-cancel').click(function () {
        $('dialog').hide();
        $('#dim').remove();
    });
})(jQuery);
