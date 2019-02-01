<?php

namespace Troyantec\Paymentez\Model\ResourceModel\Transaccion;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Troyantec\Paymentez\Model\Transaccion', 'Troyantec\Paymentez\Model\ResourceModel\Transaccion');
    }
}
