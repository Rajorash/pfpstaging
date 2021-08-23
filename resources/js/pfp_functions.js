export class pfpFunctions {
    tableStickyHeader() {
        if ($('.table-sticky-header').length) {
            $('.table-sticky-header').floatThead({
                position: 'absolute'
            });
        }
    }

    tableStickyFirstColumn() {
        if ($('.table-sticky-column').length) {
            $('.cloned_table').remove(); //remove previous cloned tables

            if ($('.table-sticky-column-place').length) {
                $(".table-sticky-column")
                    .not('.floatThead-table')
                    .clone(true)
                    .appendTo($(".table-sticky-column").closest('.table-sticky-column-place'))
                    .addClass('cloned_table');
            } else {
                $(".table-sticky-column")
                    .not('.floatThead-table')
                    .clone(true)
                    .appendTo($(".table-sticky-column").parent())
                    .addClass('cloned_table');
            }

        }
    }
}
