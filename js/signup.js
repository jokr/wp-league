(function ($, ajaxurl) {
    $('input[required]').focusout(function () {
        var value = this.value;
        if (value.trim().length == 0) {
            $(this).prev('label').addClass('error');
            $('#league-signup').prop('disabled', true);
        } else {
            $(this).prev('label').removeClass('error');
            $('#league-signup').prop('disabled', false);
        }
    });

    $('input#user_email, input#user_email_repeat').focusout(function () {
        var value = this.value;
        if (!value.match('^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$')) {
            $(this).prev('label').addClass('error');
        } else {
            $(this).prev('label').removeClass('error');
        }
    });

    $('input#user_email_repeat').focusout(function () {
        var value = this.value;
        if (value != $('input#user_email').val()) {
            $(this).prev('label').addClass('error');
        } else {
            $(this).prev('label').removeClass('error');
        }
    });

    $('input#user_dci').focusout(function () {
        var value = this.value;
        if (!value.match('^[1-9][0-9]{6,9}$')) {
            $(this).prev('label').addClass('error');
        } else {
            $(this).prev('label').removeClass('error');
        }
    });

    $('#league-register').submit(function (event) {
        var firstName = $('#user_first').value();
        var lastName = $('#user_last').value();
        var email = $('#user_email').value();
        var email2 = $('#user_email_repeat').value();

        event.preventDefault();
    });
})(jQuery, ajaxurl);