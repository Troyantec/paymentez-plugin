<?php

namespace Troyantec\Paymentez\Model;

class Transaccion extends \Magento\Framework\Model\AbstractModel implements TransaccionInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'paymentez_transaccion';

    protected function _construct()
    {
        $this->_init('Troyantec\Paymentez\Model\ResourceModel\Transaccion');
    }

    public function getIdentities()
    {
        return array(self::CACHE_TAG.'_'.$this->getId());
    }
}
