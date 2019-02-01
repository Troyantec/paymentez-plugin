<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Model;

/**
 * Pay In Store payment method model.
 */
class Paymentez extends \Magento\Payment\Model\Method\AbstractMethod
{
    // protected $_formBlockType = 'Troyantec\Paymentez\Block\Redirect\Form';
    // protected $_infoBlockType = 'Magento\Payment\Block\Info';
    protected $_isGateway = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canCaptureOnce = true;
    protected $_canRefund = true;
    protected $_canOrder = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_supportedCurrencyCodes = array('USD');
    protected $_urlInterface;
    protected $_transaccion;
    protected $_direccion;
    protected $_tpConnector = null;
    protected $_order = null;
    protected $hibrido_flag = false;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Troyantec\Paymentez\Model\Factory\Connector $tpc,
        \Magento\Framework\Url $urlInterface,
          TransaccionFactory $transaccionFactory,
          DireccionFactory $direccionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_transaccion = $transaccionFactory;
        $this->_direccion = $direccionFactory;
        $this->_urlInterface = $urlInterface;
        $this->_tpConnector = $tpc;
    }

    /**
     * Retrieve payment method title.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Paymentez';
    }

    public function isActive($storeId = null)
    {
        $todopago_active = (bool) (int) $this->getConfigData('active', $storeId);

        $hibrido = (bool) (int) $this->getConfigData('hibrido', $storeId);
        if ($todopago_active == true) {
            if ($this->_code == 'tpbille') {
                return true;
            }

            if ($hibrido == $this->hibrido_flag) {
                return true;
            }
        }

        return false;
    }

    public function canUseForCountry($country)
    {
        return true;
    }

    public function canUseForCurrency($currencyCode)
    {
        return true;
    }

    public function getConfigData($field, $storeId = null)
    {
        if ('order_place_redirect_url' === $field) {
            return $this->getOrderPlaceRedirectUrl();
        }
        if (null === $storeId) {
            $storeId = $this->getStore();
        }

        $path = 'payment/paymentez/'.$field;

        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    protected function getProductsFromOrder()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetProductsFromOrder');
        $items = $this->_order->getAllItems();

        return $items;
    }

    protected function getCustomerData()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetCustomerData');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        return $objectManager->create('Magento\Customer\Model\Customer')->load($this->_order->getCustomerId());
    }

    protected function getShippingAddress()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetShippingAddress');

        return $this->_order->getShippingAddress();
    }

    protected function getBillingAddress()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetBillingAddress');

        return $this->_order->getBillingAddress();
    }

    protected function getIp()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetIP');
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $a */
        $a = $om->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');

        return $a->getRemoteAddress();
    }

    protected function getDataComercial()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetDataComercial');
        $payDataComercial = array();
        $payDataComercial['URL_OK'] = $this->_urlInterface->getBaseUrl().'paymentez/payment/secondstep/id/'.$this->_order->getId();
        $payDataComercial['URL_ERROR'] = $this->_urlInterface->getBaseUrl().'paymentez/payment/secondstep/id/'.$this->_order->getId();
        $payDataComercial['Merchant'] = $this->_tpConnector->getMerchant();
        $payDataComercial['Security'] = $this->_tpConnector->getSecurity();
        $payDataComercial['EncodingMethod'] = 'XML';

        return $payDataComercial;
    }

    protected function getDataControlFraudeCommon($customer, $billingAddress)
    {
        // CS Common
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetDataControlFraudeCommon');
        $payDataOperacion = array();
        $payDataOperacion['CSBTCITY'] = $billingAddress->getCity();
        $payDataOperacion['CSBTCOUNTRY'] = $billingAddress->getCountryId();
        $payDataOperacion['CSBTPOSTALCODE'] = $billingAddress->getPostcode();
        $payDataOperacion['CSBTSTATE'] = substr($billingAddress->getRegion(), 0, 1);
        $payDataOperacion['CSBTSTREET1'] = implode(' ', $billingAddress->getStreet());
        $payDataOperacion['CSBTIPADDRESS'] = $this->getIp();
        $payDataOperacion['CSBTPHONENUMBER'] = $billingAddress->getTelephone();
        $payDataOperacion['CSBTSTREET2'] = '';
        $payDataOperacion['CSPTCURRENCY'] = 'ARS';
        $payDataOperacion['CSPTGRANDTOTALAMOUNT'] = number_format($this->_order->getGrandTotal(), 2, '.', '');
        $payDataOperacion['CSMDD6'] = 'Web';

        if ($this->_order->getCustomerIsGuest()) {
            $payDataOperacion['CSMDD8'] = 'Y';
            $payDataOperacion['CSMDD7'] = 1;

            $payDataOperacion['CSBTEMAIL'] = $billingAddress->getEmail();
            $payDataOperacion['CSBTFIRSTNAME'] = $billingAddress->getFirstname();
            $payDataOperacion['CSBTLASTNAME'] = $billingAddress->getLastname();
            $payDataOperacion['CSBTCUSTOMERID'] = 'guest';
        } else {
            $payDataOperacion['CSMDD8'] = 'N';
            $payDataOperacion['CSMDD9'] = $customer->getPasswordHash();
            $now = new \DateTime();
            $fecha = new \DateTime($customer->getCreatedAt());
            $payDataOperacion['CSMDD7'] = $now->diff($fecha, true)->format('%a');

            $payDataOperacion['CSBTEMAIL'] = $customer->getEmail();
            $payDataOperacion['CSBTFIRSTNAME'] = $customer->getFirstname();
            $payDataOperacion['CSBTLASTNAME'] = $customer->getLastname();
            $payDataOperacion['CSBTCUSTOMERID'] = $customer->getId();
        }
        $payDataOperacion['CSMDD11'] = $billingAddress->getTelephone();

        return $payDataOperacion;
    }

    protected function getDataControlFraudeRetail($customer, $shippingAddress)
    {
        // CS Retail
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetDataControlFraudeRetail');
        $payDataOperacion = array();
        $payDataOperacion['CSSTCITY'] = $shippingAddress->getCity();
        $payDataOperacion['CSSTCOUNTRY'] = $shippingAddress->getCountryId();
        $payDataOperacion['CSSTEMAIL'] = $shippingAddress->getEmail();
        $payDataOperacion['CSSTFIRSTNAME'] = $shippingAddress->getFirstname();
        $payDataOperacion['CSSTLASTNAME'] = $shippingAddress->getLastname();
        $payDataOperacion['CSSTPHONENUMBER'] = $shippingAddress->getTelephone();
        $payDataOperacion['CSSTPOSTALCODE'] = $shippingAddress->getPostcode();
        $payDataOperacion['CSSTSTATE'] = substr($shippingAddress->getRegion(), 0, 1);
        $payDataOperacion['CSSTSTREET1'] = implode(' ', $shippingAddress->getStreet());
        $payDataOperacion['CSMDD12'] = '10';
        $payDataOperacion['CSMDD13'] = $this->_order->getShippingDescription();
        $payDataOperacion['CSMDD16'] = '';

        return $payDataOperacion;
    }

    protected function getDataControlFraudeDetail($products)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetDataControlFraudeDetail');
        // CS Retail Product
        $productcode_array = array();
        $description_array = array();
        $name_array = array();
        $sku_array = array();
        $totalamount_array = array();
        $quantity_array = array();
        $price_array = array();

        foreach ($products as $prod) {
            $productcode_array[] = 'default';
            $desc = $prod->getDescription();
            if (empty($desc)) {
                $desc = $prod->getName();
            }
            $description_array[] = $desc;
            $name_array[] = $prod->getName();
            $sku_array[] = $prod->getSku();
            $totalamount_array[] = number_format($prod->getPrice() * $prod->getQtyOrdered(), 2, '.', '');
            $quantity_array[] = number_format($prod->getQtyOrdered(), 0, '', '');
            $price_array[] = number_format($prod->getPrice(), 2, '.', '');
        }

        $payDataOperacion = array();
        $payDataOperacion['CSITPRODUCTCODE'] = join('#', $productcode_array);
        $payDataOperacion['CSITPRODUCTDESCRIPTION'] = join('#', $description_array);
        $payDataOperacion['CSITPRODUCTNAME'] = join('#', $name_array);
        $payDataOperacion['CSITPRODUCTSKU'] = join('#', $sku_array);
        $payDataOperacion['CSITTOTALAMOUNT'] = join('#', $totalamount_array);
        $payDataOperacion['CSITQUANTITY'] = join('#', $quantity_array);
        $payDataOperacion['CSITUNITPRICE'] = join('#', $price_array);

        return $payDataOperacion;
    }

    protected function getDataOperacion($customer)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetDataOperacion');
        $payDataOperacion = array();
        $payDataOperacion['MERCHANT'] = $this->_tpConnector->getMerchant();
        $payDataOperacion['OPERATIONID'] = $this->_order->getIncrementId();
        $payDataOperacion['AMOUNT'] = number_format($this->_order->getGrandTotal(), 2, '.', '');
        $payDataOperacion['CURRENCYCODE'] = '032';
        $payDataOperacion['EMAILCLIENTE'] = $customer->getEmail();

        if ($this->_scopeConfig->getValue('payment/paymentez/timeout_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 1) {
            $payDataOperacion['TIMEOUT'] = $this->_scopeConfig->getValue('payment/paymentez/timeout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }

        if ($this->_scopeConfig->getValue('payment/paymentez/cuotas_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 1) {
            $payDataOperacion['MAXINSTALLMENTS'] = $this->_scopeConfig->getValue('payment/paymentez/cuotas_cant', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $moduleInfo = $objectManager->get('Magento\Framework\Module\ModuleList')->getOne('Troyantec_Paymentez');

        //Busca c�digo del m�todo de pago
        /*
        $payment = $this->_order->getPayment();
        $method = $payment->getMethodInstance();
        $methodTitle = $method->getCode();
        */
        if ($this->_scopeConfig->getValue('payment/paymentez/hibrido', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 1) {
            $tipo = '-H';
        } else {
            $tipo = '-E';
        }

        $version = $productMetadata->getVersion();

        $payDataOperacion['ECOMMERCENAME'] = 'MAGENTO';
        $payDataOperacion['ECOMMERCEVERSION'] = $version;
        $payDataOperacion['PLUGINVERSION'] = $moduleInfo['setup_version'].$tipo;

        return $payDataOperacion;
    }

    protected function getSha1()
    {
        $shippingAddress = $this->getShippingAddress();
        $billingAddress = $this->getBillingAddress();

        $sha1 = sha1($shippingAddress->getCountryId().$shippingAddress->getPostcode().substr($shippingAddress->getRegion(), 0, 1).implode(' ', $shippingAddress->getStreet()).$shippingAddress->getCity().$shippingAddress->getCity().$billingAddress->getCountryId().$billingAddress->getPostcode().substr($billingAddress->getRegion(), 0, 1).implode(' ', $billingAddress->getStreet()).$billingAddress->getCity().$billingAddress->getCity());

        return $sha1;
    }

    protected function getPayData()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetPayData');
        $products = $this->getProductsFromOrder();
        $customer = $this->getCustomerData();

        $datosComercio = $this->getDataComercial();
        $datosOperacion = $this->getDataOperacion($customer);
        $datosOperacionBillingAddress = $this->getDataControlFraudeCommon($customer, $this->getBillingAddress());
        $datosOperacion = array_merge($datosOperacion, $datosOperacionBillingAddress);
        $datosOperacionShippingAddress = $this->getDataControlFraudeRetail($customer, $this->getShippingAddress());
        $datosOperacion = array_merge($datosOperacion, $datosOperacionShippingAddress);
        $datosOperacion = array_merge($datosOperacion, $this->getDataControlFraudeDetail($products));

        return array($datosComercio, $datosOperacion);
    }

    protected function execSar($data)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - ExecSAR');
        $this->_logger->debug('Paymentez - MODEL PAYMENT - Request: '.json_encode($data));
        $sar_response = $this->_tpConnector->sendAuthorizeRequest($data[0], $data[1]);
        if ($sar_response['StatusCode'] == 702) {
            $this->_logger->debug('Paymentez - MODEL PAYMENT - RetrySAR');
            $sar_response = $this->_tpConnector->sendAuthorizeRequest($data[0], $data[1]);
        }
        $this->_logger->debug('Paymentez - MODEL PAYMENT - Response: '.json_encode($sar_response));

        if ($sar_response['StatusCode'] != -1) {
            throw new \Exception($sar_response['StatusMessage']);
        }

        return $sar_response;
    }

    public function firstStep($order)
    {
        $this->_logger->debug('TODOPAGO - MODEL PAYMENT - FirstStep');
        $this->_order = $order;
        $data = $this->getPayData();
        $this->_logger->debug('TODOPAGO - MODEL PAYMENT - CreateTransaction');
        $tran = $this->_transaccion->create();
        $shippingAddress = $this->getShippingAddress();
        $billingAddress = $this->getBillingAddress();

        $customer = $this->getCustomerData();
        $res = $this->execSar($data);

        $tran->setData('orderid', $this->_order->getId())
        ->setData('requestkey', $res['RequestKey'])
            ->setData('publicrequestkey', $res['PublicRequestKey'])
            ->save();
        $this->_logger->debug('Paymentez - MODEL PAYMENT - SaveTransaction');

        return $res;
    }

    public function getAmount()
    {
        return number_format($this->_order->getGrandTotal(), 2, ',', '');
    }

    public function getRequestKey($order = null)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetRequestKey');
        if ($order != null) {
            $tran = $this->_transaccion->create();

            return $tran->load($order->getId(), 'orderid')->getRequestkey();
        }
        $tran = $this->_transaccion->create();

        return $tran->load($this->_order->getId(), 'orderid')->getRequestkey();
    }

    public function getPublicRequestKey($order = null)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetRequestKey');
        if ($order != null) {
            $tran = $this->_transaccion->create();

            return $tran->load($order->getId(), 'orderid')->getPublicrequestkey();
        }
        $tran = $this->_transaccion->create();

        return $tran->load($this->_order->getId(), 'orderid')->getPublicrequestkey();
    }

    public function getAnswerKey($order = null)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetAnswerKey');
        if ($order != null) {
            $tran = $this->_transaccion->create();

            return $tran->load($order->getId(), 'orderid')->getAnswerkey();
        }
        $tran = $this->_transaccion->create();

        return $tran->load($this->_order->getId(), 'orderid')->getAnswerkey();
    }

    protected function getAnswerData($answerKey)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetAnswerData');
        $optionsAnswer = array(
            'Security' => $this->_tpConnector->getSecurity(),
            'Merchant' => $this->_tpConnector->getMerchant(),
            'RequestKey' => $this->getRequestKey(),
            'AnswerKey' => $answerKey,
        );

        return $optionsAnswer;
    }

    protected function execGaa($data)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - ExecGAA');
        $this->_logger->debug('Paymentez - MODEL PAYMENT - Request: '.json_encode($data));
        $gaa_response = $this->_tpConnector->getAuthorizeAnswer($data);
        $this->_logger->debug('Paymentez - MODEL PAYMENT - Response: '.json_encode($gaa_response));

        return $gaa_response;
    }

    public function secondStep($order, $ak)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - SecondStep');
        $this->_order = $order;
        $data = $this->getAnswerData($ak);

        $res = $this->execGaa($data);
        $this->_logger->debug('Paymentez - MODEL PAYMENT - SearchTransaction');
        $tran = $this->_transaccion->create();
        $tran->load($this->_order->getId(), 'orderid')
             ->setData('answerkey', $ak)
             ->setData('authorizationkey', $res['AuthorizationKey'])
             ->save();
        $this->_logger->debug('Paymentez - MODEL PAYMENT - UpdateTransaction');

        if ($res['StatusCode'] != -1) {
            throw new \Exception($res['StatusMessage']);
        }

        return $res;
    }

    public function getAmbiente()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetAmbiente');

        return $this->_tpConnector->getAmbiente();
    }

    public function getRestoreCart()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetRestoreCart');

        return $this->_scopeConfig->getValue('payment/paymentez/restore_cart', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getOrderStatuses()
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetOrderStatuses');

        return array(
            'inicial' => $this->_scopeConfig->getValue('payment/todopago/estado/inicial', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'aprobado' => $this->_scopeConfig->getValue('payment/todopago/estado/aprobado', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'rechazado' => $this->_scopeConfig->getValue('payment/todopago/estado/rechazado', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'offline' => $this->_scopeConfig->getValue('payment/todopago/estado/offline', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
        );
    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - Refund');
        $this->_logger->debug('Paymentez - MODEL PAYMENT - Data: '.$amount);
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetOrder');
        $order = $payment->getOrder();
        $this->_order = $order;

        $returnData = array(
            'Security' => $this->_tpConnector->getSecurity(),
            'Merchant' => $this->_tpConnector->getMerchant(),
            'RequestKey' => $this->getRequestKey(),
            'AMOUNT' => number_format($amount, 2, '.', ''),
        );
        $this->_logger->debug('Paymentez - MODEL PAYMENT - Request: '.json_encode($returnData));
        $result = $this->_tpConnector->returnRequest($returnData);
        $this->_logger->debug('Paymentez - MODEL PAYMENT - Response: '.json_encode($result));
        if ($result['StatusCode'] != 2011) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($result['StatusMessage'])
            );
        }

        return $this;
    }

    public function getMerchant()
    {
        return $this->_tpConnector->getMerchant();
    }

    public function getStatus($order = null)
    {
        $this->_logger->debug('Paymentez - MODEL PAYMENT - GetStatus');
        if ($order == null) {
            $order = $this->_order;
        }

        $this->_logger->debug('Paymentez - MODEL PAYMENT - Request: MERCHANT: '.$this->_tpConnector->getMerchant().' - OPERATIONID: '.$order->getIncrementId());
        $res = $this->_tpConnector->getStatus(array('MERCHANT' => $this->_tpConnector->getMerchant(), 'OPERATIONID' => $order->getIncrementId()));
        $this->_logger->debug('Paymentez - MODEL PAYMENT - Response: '.json_encode($res));

        return $res;
    }
}
