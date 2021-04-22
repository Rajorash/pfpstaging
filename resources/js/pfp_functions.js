$(function () {
    class pfpFunctions {
        tableStickyHeader() {
            if ($('.table-sticky-header').length) {
                $('.table-sticky-header').floatThead({
                    position: 'absolute'
                });
            }
        }

        tableStickyFirstColumn() {
            if ($('.table-sticky-first-column').length) {
                $('.table-sticky-first-column').stickyColumn({columns: 1});
            }
        }
    }
});
