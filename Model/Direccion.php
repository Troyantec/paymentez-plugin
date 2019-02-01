<?php

namespace Troyantec\Paymentez\Model;

class Direccion extends \Magento\Framework\Model\AbstractModel implements DireccionInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'paymentez_direccion';

    protected function _construct()
    {
        $this->_init('Troyantec\Paymentez\Model\ResourceModel\Direccion');
    }

    public function getIdentities()
    {
        return array(self::CACHE_TAG.'_'.$this->getId());
    }
}
