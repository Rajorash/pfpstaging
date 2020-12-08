(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/app"],{

/***/ "./resources/js/allocations.js":
/*!*************************************!*\
  !*** ./resources/js/allocations.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/**
 * Send the details of the update request through
 * to /allocations/update
 */
var updateAllocation = function updateAllocation(e) {
  var allocation = {
    'id': $(this).data('id'),
    'allocation_type': $(this).data('type'),
    'amount': $(this).val(),
    'allocation_date': $(this).data('date'),
    '_token': $('meta[name="csrf-token"]').attr('content')
  };
  $.post('/allocations/update', allocation).done(function (data) {// console.log(data);
  });
};
/**
 *  Cascade through all account flows and calculate the account total
 */


var calculateAccountTotal = function calculateAccountTotal(e) {
  var accountId = $(this).data('parent');
  var date = $(this).data('date');
  var total = 0;
  var flows = $('.flow input[data-parent=' + accountId + '][data-date=' + date + ']');
  flows.each(function () {
    var value = parseInt($(this).val());

    if (!value) {
      value = 0;
    }

    if ($(this).data('direction') == 'negative') {
      total = total - value;
    } else {
      total = total + value;
    }
  });
  var accountInput = $('.account-value[data-id="' + accountId + '"][data-date=' + date + ']');
  accountInput.val(total);
  accountInput.trigger('change'); // return total;
};

var calculateProjectedTotal = function calculateProjectedTotal(e) {
  var _parseFloat;

  var hierarchy = $(this).data('hierarchy');
  var date = $(this).data('date');
  var percentage = (_parseFloat = parseFloat($(this).parent().data('percentage') / 100)) !== null && _parseFloat !== void 0 ? _parseFloat : 0; // sum all the values from revenue account (should be 1 account...)

  var revenue = 0;
  var salestax = calculateHierarchyValueOnDate(date, 'salestax');
  var prereal = calculateHierarchyValueOnDate(date, 'prereal');
  var postreal = calculateHierarchyValueOnDate(date, 'postreal');
  var pretotal = calculateHierarchyValueOnDate(date, 'pretotal');
  var revenueOnDate = $(".account-value[data-hierarchy='revenue'][data-date='".concat(date, "']"));
  revenueOnDate.each(function () {
    revenue = parseInt(revenue) + parseInt($(this).val());
  });
  var receiptsToAllocate = parseInt(revenue + pretotal); // percentage is passed as zero on no sales tax accounts - figure out how to keep date specific salestax percentage

  var salestaxPercentage = $(".account[data-hierarchy='salestax'][data-date='".concat(date, "']")).data('percentage');
  var salestaxDivisor = salestaxPercentage / 100 + 1;
  var netCashReceipts = Math.round(receiptsToAllocate / salestaxDivisor);
  var realRevenue = parseInt(netCashReceipts) + parseInt(prereal);
  var projectedTotalField = $(this).parent().find(".projected-total");
  var placeholderValue = revenue;

  switch (hierarchy) {
    case 'revenue':
      placeholderValue = parseInt(getAdjustedDailyAccountTotal(projectedTotalField) + getPreviousProjectedTotal(projectedTotalField));
      break;

    case 'pretotal':
      placeholderValue = calculatePretotalPlaceholder(projectedTotalField);
      break;

    case 'salestax':
      placeholderValue = parseInt(receiptsToAllocate) - parseInt(netCashReceipts);
      break;

    case 'prereal':
      placeholderValue = parseInt(netCashReceipts * percentage);
      break;

    case 'postreal':
      placeholderValue = parseInt(realRevenue * percentage);
      break;
  }

  projectedTotalField.attr('placeholder', placeholderValue);
};

function calculateHierarchyValueOnDate(date, hierarchy) {
  var selector = $(".account-value[data-hierarchy='".concat(hierarchy, "'][data-date='").concat(date, "']"));
  var value = 0;
  selector.each(function () {
    var valueOnDate = $(this).parent().find('.projected-total').attr('placeholder');
    value = parseInt(value) + parseInt(valueOnDate);
  });
  return value;
}

function calculatePretotalPlaceholder(projectedTotalField) {
  var dayTotal = getAdjustedDailyAccountTotal(projectedTotalField);
  var previousProjected = getPreviousProjectedTotal(projectedTotalField);
  return parseInt(dayTotal + previousProjected);
}

function getPreviousProjectedTotal(currentProjectedTotalField) {
  // get the col id from the passed projected total input
  var col = currentProjectedTotalField.parent().data('col');
  var row = currentProjectedTotalField.parent().data('row'); // if this is the first entry, return 0

  if (col === 1) {
    return parseInt(0);
  } // locate the previous column


  col = parseInt(col) - 1;
  var previousProjectedTotalField = $(".account[data-col='".concat(col, "'][data-row='").concat(row, "'] .projected-total"));
  var previousProjectedTotal = previousProjectedTotalField.attr('placeholder');

  if (previousProjectedTotalField.val()) {
    previousProjectedTotal = parseInt(previousProjectedTotalField.val());
  }

  return parseInt(previousProjectedTotal);
}

function getAdjustedDailyAccountTotal(currentProjectedTotalField) {
  var adjustedAccountTotalField = currentProjectedTotalField.parent().find(".account-value");
  return parseInt(adjustedAccountTotalField.val());
}

function setCumulativeTotal(targetField) {
  var row = targetField.parent().data('row');
  var col = targetField.parent().data('col');
  var value = 0; // if this is not the first column, get the previous cumulative total

  if (col > 1) {
    var previousTotalField = $(".account[data-col=\"".concat(col - 1, "\"][data-row='").concat(row, "'] .cumulative")).first();
    var previousTotal = parseInt(previousTotalField.val());
    value = value + parseInt(previousTotal);
  } // get the adjusted day total


  var accountRow = $(".account[data-col='".concat(col, "'][data-row='").concat(row, "']")).first();
  var accountValueField = accountRow.find(".account-value").first();
  var projectedTotalField = accountRow.find(".projected-total").first();
  var adjTotal = parseInt(accountValueField.val()) + parseInt(projectedTotalField.attr('placeholder')); // revenue accounts do not accumulate projected total

  if (accountRow.data('hierarchy') == 'revenue') {
    adjTotal = parseInt(accountValueField.val());
  }

  value = value + adjTotal;
  targetField.val(parseInt(value));
} // upon changing the value of a flow input, update the Allocation in the DB


$('.allocation-value').on("change", updateAllocation); // if an AccountFlow is updated, calculate the new BankAccount total

$('.flow .allocation-value').on("change", calculateAccountTotal); // if an account allocation changes, update all account allocations

$('.account .allocation-value').on("change", $.each($('.account .allocation-value'), calculateProjectedTotal)); // calculate projected values

$.each($('.account .allocation-value'), calculateProjectedTotal); // calculate cumulative totals based on previous and current values for each date

$('.cumulative').each(function () {
  setCumulativeTotal($(this));
}); // if anything in the table changes, roll all calculations again to update the values

$('#allocationTable').on('change', function () {
  $.each($('.account .allocation-value'), calculateProjectedTotal);
  $('.cumulative').each(function () {
    setCumulativeTotal($(this));
  });
});

/***/ }),

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */
__webpack_require__(/*! ./bootstrap */ "./resources/js/bootstrap.js");
/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


__webpack_require__(/*! ./components/Example */ "./resources/js/components/Example.js");

__webpack_require__(/*! ./allocations */ "./resources/js/allocations.js");

__webpack_require__(/*! ./percentages */ "./resources/js/percentages.js");

/***/ }),

/***/ "./resources/js/bootstrap.js":
/*!***********************************!*\
  !*** ./resources/js/bootstrap.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

window._ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  window.Popper = __webpack_require__(/*! popper.js */ "./node_modules/popper.js/dist/esm/popper.js")["default"];
  window.$ = window.jQuery = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");

  __webpack_require__(/*! bootstrap */ "./node_modules/bootstrap/dist/js/bootstrap.js");
} catch (e) {}
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */


window.axios = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
// import Echo from 'laravel-echo';
// window.Pusher = require('pusher-js');
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

/***/ }),

/***/ "./resources/js/components/Example.js":
/*!********************************************!*\
  !*** ./resources/js/components/Example.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "./node_modules/react/index.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-dom */ "./node_modules/react-dom/index.js");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_1__);



function Example() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
    className: "container"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
    className: "row justify-content-center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
    className: "col-md-8"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
    className: "card"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
    className: "card-header"
  }, "Example Component"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
    className: "card-body"
  }, "I'm an example component!")))));
}

/* harmony default export */ __webpack_exports__["default"] = (Example);

if (document.getElementById('example')) {
  react_dom__WEBPACK_IMPORTED_MODULE_1___default.a.render( /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(Example, null), document.getElementById('example'));
}

/***/ }),

/***/ "./resources/js/percentages.js":
/*!*************************************!*\
  !*** ./resources/js/percentages.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/**
 * Send the details of the update request through
 * to /percentages/update
 */
var updateAllocationPercentage = function updateAllocationPercentage(e) {
  var percentage = {
    'phase_id': $(this).data('phase-id'),
    'bank_account_id': $(this).data('account-id'),
    'percent': $(this).val(),
    '_token': $('meta[name="csrf-token"]').attr('content')
  }; // console.table([percentage]);

  $.post('/percentages/update', percentage).done(function (data) {
    console.log(data);
  });
}; // calculate the current total percentage value of each phase


var updatePercentageTotal = function updatePercentageTotal(e) {
  var phase_id = $(this).data('phase-id');
  console.log("Function ".concat(phase_id));
  var percentageTotalField = $(".percentage-total[data-phase-id='".concat(phase_id, "']"));
  var total = calculatePhaseTotal(phase_id);

  if (total > 100) {
    percentageTotalField.addClass('text-danger');
  } else {
    percentageTotalField.removeClass('text-danger');
  }

  percentageTotalField.text("".concat(total, "%"));
};

function calculatePhaseTotal(phase_id) {
  var phasePercentagesFields = $(".percentage-value[data-phase-id='".concat(phase_id, "']"));
  var total = 0;
  phasePercentagesFields.each(function () {
    var value = parseFloat($(this).val());

    if (!isNaN(value)) {
      total = total + value;
    }
  });
  return total;
} // set initial values


$.each($('.percentage-total'), updatePercentageTotal); // upon changing the value of a flow input, update the AllocationPercentage in the DB

$('.percentage-value').on("change", updateAllocationPercentage); // upon changing the value of a flow input, update the total value below

$('.percentage-value').on("change", updatePercentageTotal);

/***/ }),

/***/ "./resources/sass/app.scss":
/*!*********************************!*\
  !*** ./resources/sass/app.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!*************************************************************!*\
  !*** multi ./resources/js/app.js ./resources/sass/app.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! C:\laragon\www\pfp-dev\resources\js\app.js */"./resources/js/app.js");
module.exports = __webpack_require__(/*! C:\laragon\www\pfp-dev\resources\sass\app.scss */"./resources/sass/app.scss");


/***/ })

},[[0,"/js/manifest","/js/vendor"]]]);