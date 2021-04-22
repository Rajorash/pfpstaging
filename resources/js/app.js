require('./bootstrap');
require('alpinejs');

require('arrow-table');
// require('./allocations');
// require('./percentages');
require('./pfp_functions');
require('./allocation_calculator');
require('./percentages_calculator');
require('./jquery-3.4.1.slim.min');
require('./jquery.floatThead.min');
require('./stickycolumn');

$('.global_nice_scroll').niceScroll();
let resizeTimer;
$(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        $(".global_nice_scroll").getNiceScroll().resize();
    }, 300);
});

function tableStickyHeader() {
    if ($('.table-sticky-header').length) {
        $('.table-sticky-header').floatThead({
            position: 'absolute'
        });
    }
}

function tableStickyFirstColumn() {
    // if ($('.table-sticky-first-column').length) {
    //     $('.table-sticky-first-column').stickyColumn({columns: 1});
    // }
}

tableStickyHeader();
tableStickyFirstColumn();
