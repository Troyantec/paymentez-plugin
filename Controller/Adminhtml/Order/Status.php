<?php
/**
 * Copyright � 2015 Inchoo d.o.o.
 * created by Zoran Salamun(zoran.salamun@inchoo.net).
 */

namespace Troyantec\Paymentez\Controller\Adminhtml\Order;

class Status extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    protected $pageFactory;

    protected $_tpConnector;

    protected $_publicActions = ['execute', 'status'];

    protected $_error = array();

    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Troyantec\Paymentez\Model\Factory\Connector $tpc
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $logger;
        $this->pageFactory = $pageFactory;
        $this->_tpConnector = $tpc;
    }

    protected function callService($order)
    {
        $todopago_connector = $this->_tpConnector;
        $this->_logger->debug('PAYMENTEZ - GETSTATUS - MERCHANT: '.$this->_tpConnector->getMerchant().' - OPERATIONID: '.$order->getIncrementId());
        $res = $todopago_connector->getStatus(array('MERCHANT' => $this->_tpConnector->getMerchant(), 'OPERATIONID' => $order->getIncrementId()));
        $this->_logger->debug('PAYMENTEZ - GETSTATUS RESPONSE: '.json_encode($res));

        return $res;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('order_id');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order_model = $objectManager->get('Magento\Sales\Model\Order');
        $order = $order_model->load($id);

        $page_object = $this->pageFactory->create();
        $block = $page_object->getLayout()->getBlock('paymentez_order_status');
        $block->setStatus($this->callService($order));

        return $page_object;
    }
}
