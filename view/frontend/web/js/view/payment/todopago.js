/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'tpredirect',
                component: 'Prisma_TodoPago/js/view/payment/method-renderer/tpredirect-method'
            },
            {
                type: 'tphibrido',
                component: 'Prisma_TodoPago/js/view/payment/method-renderer/tphibrido-method'
            },
            {
                type: 'tpbille',
                component: 'Prisma_TodoPago/js/view/payment/method-renderer/tpbille-method'
            },
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);