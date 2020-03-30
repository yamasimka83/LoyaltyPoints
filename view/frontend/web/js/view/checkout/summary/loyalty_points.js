define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'ko',
        'Magento_Checkout/js/model/error-processor',
        'jquery',
        'Magento_Checkout/js/action/get-totals'
    ],
    function (Component, quote, priceUtils, totals, ko, errorProcessor, $, getTotals) {
        "use strict";
        return Component.extend({

            totals: quote.getTotals(),

            isDisabledPoints: function () {
                return this.getPureValue() != 0;
            },

            isDisplayedInfo: function () {
                return this.getFullValue() != 0;
            },

            title: function () {
                return totals.getSegment('loyalty_points').title;
            },

            getValue: function () {
                let price = 0;
                if (this.totals()) {
                    price = totals.getSegment('loyalty_points').value[0];
                }
                return this.getFormattedPrice(price);
            },
            getPureValue: function () {
                let price = 0;
                if (this.totals()) {
                    price = totals.getSegment('loyalty_points').value[0];
                }
                return price;
            },
            getFullValue: function () {
                let price = 0;
                if (this.totals()) {
                    price = totals.getSegment('loyalty_points').value[1];
                }
                return price;
            },
            applyLoyaltyPoints: function () {
                return $.ajax({
                    url: "http://magento2.loc/customer/totals/collect",
                    data: {
                        'isUsePoints': true,
                        'quoteId': quote.getQuoteId()
                        },
                    type: "POST",
                    dataType: 'json',
                }).done(function () {
                    getTotals([], false);
                }).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                );
            },
            unsetLoyaltyPoints: function () {
                return $.ajax({
                    url: "http://magento2.loc/customer/totals/collect",
                    data: {
                        'isUsePoints': false,
                        'quoteId': quote.getQuoteId()
                    },
                    type: "POST",
                    dataType: 'json',
                }).done(function () {
                    getTotals([], false);
                }).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                );
            }
        });
    }
);
