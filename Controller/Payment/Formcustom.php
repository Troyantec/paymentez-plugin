<?php
/**
 * Copyright � 2015 Inchoo d.o.o.
 * created by Zoran Salamun(zoran.salamun@inchoo.net).
 */

namespace Troyantec\Paymentez\Controller\Payment;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Payment\Transaction;

class Formcustom extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $transactionRepository;
    /**
     * @var Transaction\BuilderInterface
     */
    protected $transactionBuilder;

    protected $pageFactory;

    protected $_publicActions = ['execute', 'formcustom'];

    /**
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
        PaymentHelper $paymentHelper,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_logger = $logger;
        $this->_paymentHelper = $paymentHelper;
        $this->transactionRepository = $transactionRepository;
        $this->transactionBuilder = $transactionBuilder;
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        $this->_logger->debug('TODOPAGO - FORMCUSTOM INIT');

        $id = $this->getRequest()->getParam('id');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order_model = $objectManager->get('Magento\Sales\Model\Order');
        $order = $order_model->load($id);

        $method = $order->getPayment()->getMethod();
        $methodInstance = $this->_paymentHelper->getMethodInstance($method);

        $codeMethod = $methodInstance->getCode();

        $page_object = $this->pageFactory->create();
        $block = $page_object->getLayout()->getBlock('todopago_formcustom');

        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load($order->getCustomerId());

        $block->setAmbiente($methodInstance->getAmbiente());
        $block->setRequestKey($methodInstance->getPublicRequestKey($order));
        $block->setMerchant($methodInstance->getMerchant());
        $block->setAmount(number_format($order->getGrandTotal(), 2, '.', ''));
        $block->setMail($customer->getEmail());
        $block->setcodeMethod($codeMethod);

        $apyn = $customer->getFirstname().' '.$customer->getLastname();

        if ($apyn == ' ') {
            $apyn = '';
        }

        $block->setNombre($apyn);
        $block->setOrden($order->getId());

        return $page_object;
    }
}
