define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'ko'
    ],
    function (Component, quote, priceUtils, totals, ko) {
        "use strict";
        return Component.extend({

            totals: quote.getTotals(),

            isVisible: ko.observable(false),

            title: function () {
                return totals.getSegment('loyalty_points').title;
            },

            isDisplayed: function () {
                return this.getPureValue() != 0;
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
            showPoints: function () {
                this.isVisible = this.isVisible ? false : true;
                console.log(this);
                location.reload();
                console.log(this);
            }
        });
    }
);
