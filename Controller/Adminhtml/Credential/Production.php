<?php
/**
 * Copyright � 2015 Inchoo d.o.o.
 * created by Zoran Salamun(zoran.salamun@inchoo.net).
 */

namespace Troyantec\Paymentez\Controller\Adminhtml\Credential;

class Production extends \Magento\Backend\App\Action
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

    protected $_publicActions = ['execute', 'production'];

    protected $_error = array();

    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $logger;
        $this->pageFactory = $pageFactory;
        $this->_tpConnector = new \Troyantec\Paymentez\Model\Factory\Connector($scopeConfig, 'prod');
    }

    protected function callService()
    {
        $this->_logger->debug('TODOPAGO - CREDENTIAL - Production');
        $user = new \TodoPago\Data\User($this->getRequest()->getPost('email'), $this->getRequest()->getPost('pass'));
        $todopago_connector = $this->_tpConnector;
        try {
            $this->_logger->debug('TODOPAGO - CREDENTIAL - Request: '.json_encode($user));
            $user = $todopago_connector->getCredentials($user);
        } catch (\Exception $e) {
            $this->_error[] = $e->getMessage();
        }
        $this->_logger->debug('TODOPAGO - CREDENTIAL - Response: '.json_encode($user));

        return $user;
    }

    public function execute()
    {
        $page_object = $this->pageFactory->create();
        if (!empty($this->getRequest()->getParam('email'))) {
            $user = $this->callService();
            if (count($this->_error) == 0) {
                $sec = explode(' ', $user->getApikey());
                echo '<script>window.opener.document.getElementsByName("groups[todopago][groups][ambiente_g][groups][produccion][fields][merchant][value]")[0].value="'.$user->getMerchant().'";</script>';
                echo '<script>window.opener.document.getElementsByName("groups[todopago][groups][ambiente_g][groups][produccion][fields][security][value]")[0].value="'.$sec[1].'";</script>';
                echo '<script>window.opener.document.getElementsByName("groups[todopago][groups][ambiente_g][groups][produccion][fields][apikey][value]")[0].value="'.$user->getApikey().'";</script>';
                echo '<script>window.close();</script>';
                die;
            } else {
                $block = $page_object->getLayout()->getBlock('todopago_credential');
                $block->setErrors($this->_error);
            }
        }

        return $page_object;
    }
}
