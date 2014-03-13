(function ($) {
    var $prize_pool = $('#cur-pool');
    var $prizes = $('input.credit-points');
    var total_prize_pool;
    var $winner = $('input.league-winner');

    function set_total() {
        total_prize_pool = 0;
        $prizes.each(function (index, element) {
            total_prize_pool += parseInt($(element).val());
        });
        $prize_pool.val(total_prize_pool);
    }

    $prizes.change(set_total);

    set_total();

    $winner.change(function () {
        if (this.checked) {
            var c = this;
            var $current = $winner.filter(':checked');
            $current.each(function (index, element) {
                if (element != c) {
                    element.checked = false;
                }
            });
        }
    });
})(jQuery);
