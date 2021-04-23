require('./bootstrap');
require('alpinejs');

require('arrow-table');
// require('./allocations');
// require('./percentages');
require('./pfp_functions');
require('./allocation_calculator');
require('./percentages_calculator');
require('./jquery.floatThead.min');

$('.global_nice_scroll').niceScroll({
    cursorwidth: '10px'
});

let resizeTimer;

$(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        $(".global_nice_scroll").getNiceScroll().resize();
    }, 300);
});

import {pfpFunctions} from "./pfp_functions.js";

let pfpFunctionsGlobal = new pfpFunctions();
pfpFunctionsGlobal.tableStickyHeader();
pfpFunctionsGlobal.tableStickyFirstColumn();
