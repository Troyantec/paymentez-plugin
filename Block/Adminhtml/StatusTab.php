<?php

namespace Troyantec\Paymentez\Block\Adminhtml;

use Magento\Payment\Helper\Data as PaymentHelper;

class StatusTab extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;

    /**
     * Collection factory.
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry             $coreRegistry
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PaymentHelper $paymentHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_paymentHelper = $paymentHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Paymentez Status');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Paymentez Status');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        $order = $this->getOrder();
        $method = $order->getPayment()->getMethod();
        $methodInstance = $this->_paymentHelper->getMethodInstance($method);

        if (method_exists($methodInstance, 'getAnswerKey') == null) {
            return false;
        } elseif ($methodInstance->getAnswerKey($order) == null) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter.
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Return URL link to Tab content.
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('paymentez/order/status', ['_current' => true]);
    }

    /**
     * Tab should be loaded trough Ajax call.
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
}
