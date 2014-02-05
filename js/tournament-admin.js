(function ($) {

    var tournamentDateElement = $('#tournament-date')
    var tournamentTimeElement = $('#tournament-time')
    var leagueElement = $('#tournament-league');
    var leagues = [];

    tournamentDateElement.datepicker({
        defaultDate: "+1w",
        dateFormat: 'yy-mm-dd',
        firstDay: 1
    });

    function update_daterange() {
        var value = leagueElement.val();
        var league = $.grep(leagues, function (e) {
            return e.id == value
        });

        function update() {
            tournamentDateElement.datepicker('option', 'minDate', league.start);
            tournamentDateElement.datepicker('option', 'maxDate', league.end);
        }

        if (league.length == 0) {
            $.post(ajaxurl, {action: 'get_league', league: 1}, function (response) {
                league = response;
                leagues.push(response);
                update();
            });
        } else {
            league = league[0];
            update();
        }
    }

    leagueElement.change(update_daterange);
    update_daterange();

    tournamentTimeElement.timepicker(
        {'step': 15, 'timeFormat': 'H:i', 'scrollDefaultTime': '18:00'}
    );

    function updateHiddenField() {
        $('#tournament-date-field').val(tournamentDateElement.val() + ' ' + tournamentTimeElement.val());
    }

    tournamentDateElement.change(updateHiddenField);
    tournamentTimeElement.change(updateHiddenField);
})(jQuery);
