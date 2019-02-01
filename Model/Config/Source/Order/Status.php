<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Model\Config\Source\Order;

use Magento\Sales\Model\Order;

/**
 * Order Status source model.
 */
class Status extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = null;
}
