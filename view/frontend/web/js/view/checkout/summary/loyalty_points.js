define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'Magento_Customer/js/model/customer'
    ],
    function (Component, quote, priceUtils, totals, customer) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'LoyaltyGroup_LoyaltyPoints/checkout/summary/loyalty_points'
            },
            totals: quote.getTotals(),
            isLoggedIn: function() {
                return  customer.isLoggedIn() && (this.getPureValue() !== 0);
            },

            getPointsTotal: function() {
                let discountSegments;

                if (!this.totals()) {
                    return null;
                }

                discountSegments = this.totals()['total_segments'].filter(function (segment) {
                    return segment.code.indexOf('loyalty_points') !== -1;
                });
                console.dir(discountSegments);
                return discountSegments.length ? discountSegments[0] : null;
            },
            getTitle: function () {
                return this.title;
            },
            getPureValue: function () {
                let pointsTotal = this.getPointsTotal();
                alert('pointsTotal' + pointsTotal.value);
                return pointsTotal ? pointsTotal.value : null;
            },
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            },
            title: 'Loyalty Points'
        });
    }
);
