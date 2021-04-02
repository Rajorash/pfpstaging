require('./bootstrap');
require('alpinejs');

require('arrow-table');
// require('./allocations');
// require('./percentages');
require('./allocation_calculator');

$('.global_nice_scroll').niceScroll();
let resizeTimer;
$(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        $(".global_nice_scroll").getNiceScroll().resize();
    }, 300);
});
