/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
[
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/action/place-order',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/payment/additional-validators',
    'mage/url',
],
function (
    $,
    Component,
    placeOrderAction,
    selectPaymentMethodAction,
    customer,
    checkoutData,
    additionalValidators,
    url) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Prisma_TodoPago/payment/tphibrido'
        },

        placeOrder: function (data, event) {
            if (event) {
                event.preventDefault();
            }
            var self = this,
                placeOrder,
                emailValidationResult = customer.isLoggedIn(),
                loginFormSelector = 'form[data-role=email-with-possible-login]';
            if (!customer.isLoggedIn()) {
                $(loginFormSelector).validation();
                emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
            }
            if (emailValidationResult && this.validate() && additionalValidators.validate()) {
                this.isPlaceOrderActionAllowed(false);
                placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                $.when(placeOrder).fail(function () {
                    self.isPlaceOrderActionAllowed(true);
                }).done(this.afterPlaceOrder.bind(this));
                return true;
            }
            return false;
        },

        selectPaymentMethod: function() {
            selectPaymentMethodAction(this.getData());
            checkoutData.setSelectedPaymentMethod(this.item.method);
            return true;
        },

        afterPlaceOrder: function () {
			jQuery.get(url.build('todopago/payment/data'), function( data ) {
					if(typeof data.error !== 'undefined') {
						window.location = data.url;
						return;
                                        }
					var body = document.body, html = document.documentElement;

					var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
					jQuery('body').append("<div id='overlay'></div>");
					jQuery('#overlay').css({'height': height+'px'});
					jQuery('#overlay').append("<div id='lightbox'></div>");
					jQuery('#lightbox').append('<iframe width="100%" height="100%" src="'+data.url+'"></iframe>');
					jQuery(".loading-mask").css({"display":"none"});
			});
		}
    });
}
);
