<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Model;

/**
 * Pay In Store payment method model.
 */
class TpHibrido extends Paymentez
{
    protected $_code = 'tphibrido';
    protected $hibrido_flag = true;

    public function getCustomUrl()
    {
        return $this->_urlInterface->getBaseUrl().'paymentez/payment/formcustom/id/'.$this->_order->getId();
    }

    public function getErrorUrl()
    {
        return $this->_urlInterface->getBaseUrl().'checkout/cart';
    }
}
