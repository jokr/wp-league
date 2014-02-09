(function ($, ajaxurl) {
    function show_details(element) {
        $(element).show();
        $('#wrapper').before('<div id="dim"></div>');

        $(element).click(function() {
            $('dialog').hide();
            $('#dim').remove();
        })
    }

    $('.league-schedule > tbody > tr.tournament-closed').click(function () {
        var $this = $(this);
        var id = parseInt($(this).children('td.column-id').html());
        var $dialog = $('dialog#tournament-' + id).get(0);

        if ($dialog) {
            show_details($dialog);
        } else {
            $.get(ajaxurl, {action: 'get_tournament_standings', tournament: id }, function (response) {
                var $table = $this.closest('.league-schedule');
                $table.before('<dialog id="tournament-' + id + '" class="tournament-standings"><div>' + response + '</div></dialog>');
                var $dialog = $('dialog#tournament-' + id).get(0);
                show_details($dialog);
            }, 'html');
        }
    });
}(jQuery, ajaxurl));
