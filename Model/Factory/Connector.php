<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Model\Factory;

class Connector
{
    protected $_scopeConfig;
    protected $_sdk;

    protected $_ambiente;
    protected $_merchant;
    protected $_apikey;
    protected $_security;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        $mode = null
    ) {
        $this->_scopeConfig = $scopeConfig;

        if ($mode == null) {
            $this->_ambiente = $this->_scopeConfig->getValue('payment/paymentez/ambiente', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } else {
            $this->_ambiente = $mode;
        }

        if ($this->_ambiente == 'prod') {
            $this->_merchant = $this->_scopeConfig->getValue('payment/paymentez/ambiente_g/produccion/merchant', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->_apikey = $this->_scopeConfig->getValue('payment/paymentez/ambiente_g/produccion/apikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->_security = $this->_scopeConfig->getValue('payment/paymentez/ambiente_g/produccion/security', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } else {
            $this->_merchant = $this->_scopeConfig->getValue('payment/paymentez/ambiente_g/developers/merchant', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->_apikey = $this->_scopeConfig->getValue('payment/paymentez/ambiente_g/developers/apikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->_security = $this->_scopeConfig->getValue('payment/paymentez/ambiente_g/developers/security', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }

        $this->_sdk = new \TodoPago\Sdk($this->getHeader(), $this->_ambiente);
    }

    protected function getHeader()
    {
        $header = json_decode($this->_apikey, true);
        if ($header == null) {
            $header = array('Authorization' => $this->_apikey);
        }

        return $header;
    }

    public function __call($method, $params = array())
    {
        return call_user_func_array(array($this->_sdk, $method), $params);
    }

    public function getMerchant()
    {
        return $this->_merchant;
    }

    public function getSecurity()
    {
        return $this->_security;
    }

    public function getAmbiente()
    {
        return $this->_ambiente;
    }

    public function setGoogleClient()
    {
        $g = new \TodoPago\Client\Google();
        $this->_sdk->setGoogleClient($g);
    }
}
