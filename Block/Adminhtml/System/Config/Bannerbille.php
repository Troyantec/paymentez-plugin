<?php
/**
 * Copyright 2019 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Block\Adminhtml\System\Config;

/**
 * Custom renderer for PayPal API credentials wizard popup.
 */
class Bannerbille extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Path to block template.
     */
    const WIZARD_TEMPLATE = 'system/config/bannerbille.phtml';

    /**
     * Set template to itself.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::WIZARD_TEMPLATE);
        }

        return $this;
    }

    /**
     * Unset some non-related element parameters.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Get the button and scripts contents.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $moduleInfo = $objectManager->get('Magento\Framework\Module\ModuleList')->getOne('Prisma_TodoPago');

        $originalData = $element->getOriginalData();

        $this->addData(
            [
                // Live
                'version_instalada' => 'Instalada: '.$moduleInfo['setup_version'],
            ]
        );

        return $this->_toHtml();
    }

    /**
     * Devuelve versi�n en Github.
     *
     * @param array $requestData
     *
     * @return string
     */
    public function buscarConfig()
    {
        /*

        $this->_scopeConfig->getValue('payment/paymentez/cuotas_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)

        */
        $this->_logger->debug('Banner inicio');

        $banner = $this->_scopeConfig->getValue('payment/paymentez/bannerbille', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $banner;
    }

    /**
     * Create request query.
     *
     * @param array $requestData
     *
     * @return string
     */
    private function createQuery(array $requestData)
    {
        $query = [];
        foreach ($requestData as $name => $value) {
            $query[] = sprintf('%s=%s', $name, $value);
        }

        return implode('&', $query);
    }
}
