(function ($) {

    var from = $('.daterange-from > input.datepicker');
    var to = $( ".daterange-to > input.datepicker" );

    from.datepicker({
        defaultDate: "+1w",
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        onClose: function( selectedDate ) {
            to.datepicker( "option", "minDate", selectedDate );
        }
    });

    to.datepicker({
        defaultDate: "+1w",
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        onClose: function( selectedDate ) {
            from.datepicker( "option", "maxDate", selectedDate );
        }
    });
})(jQuery);