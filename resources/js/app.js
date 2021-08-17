require('./bootstrap');
require('alpinejs');

require('jquery.cookie');
require('arrow-table');

// require('./allocations');
// require('./percentages');
require('./pfp_functions');
require('./allocation_calculator');
require('./percentages_calculator');
require('./projections_calculator');
require('./jquery.floatThead.min');

$('.global_nice_scroll').niceScroll({
    cursorwidth: '10px',
    zindex: 20
});

let resizeTimer;

$(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        $(".global_nice_scroll").getNiceScroll().resize();
    }, 300);
});
