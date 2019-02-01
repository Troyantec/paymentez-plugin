<?php

namespace Troyantec\Paymentez\Model\ResourceModel;

class Direccion extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('paymentez_direcciones_googemaps', 'id');
    }
}
