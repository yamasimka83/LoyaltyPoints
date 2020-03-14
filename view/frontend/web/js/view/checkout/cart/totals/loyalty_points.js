define(
    [
        'LoyaltyGroup_LoyaltyPoints/js/view/checkout/summary/loyalty_points'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            /**
             * @override
             */
            isDisplayed: function () {
                return this.getPureValue() !== 0;
            }
        });
    }
);
