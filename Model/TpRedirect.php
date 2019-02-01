<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Model;

/**
 * Pay In Store payment method model.
 */
class TpRedirect extends Paymentez
{
    protected $_code = 'tpredirect';
    protected $hibrido_flag = false;
}
