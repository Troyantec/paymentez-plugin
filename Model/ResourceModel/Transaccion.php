<?php

namespace Troyantec\Paymentez\Model\ResourceModel;

class Transaccion extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('paymentez_transacciones', 'id');
    }
}
