require('./bootstrap');
require('alpinejs');

require('jquery.cookie');
require('arrow-table');

// require('./percentages');
require('./pfp_functions');
require('./allocation_calculator');
require('./allocations');
require('./percentages_calculator');
require('./projections_calculator');
require('./revenue_calculator');
require('./jquery.floatThead.min');

let resizeTimer;

$('.global_nice_scroll').niceScroll({
    cursorwidth: '10px',
    zindex: 20
});

$(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        $(".global_nice_scroll").getNiceScroll().resize();
    }, 300);
});
