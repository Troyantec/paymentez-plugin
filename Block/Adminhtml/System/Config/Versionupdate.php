<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Block\Adminhtml\System\Config;

/**
 * Custom renderer for PayPal API credentials wizard popup.
 */
class Versionupdate extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Path to block template.
     */
    const WIZARD_TEMPLATE = 'system/config/versionupdate.phtml';

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
     * Devuelve versión en Github.
     *
     * @param array $requestData
     *
     * @return string
     */
    public function buscarGithub()
    {
        $this->_logger->debug('Github inicio');

        $versionInstalada = null;

        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $curl = $objectManager->get('\Magento\Framework\HTTP\Client\Curl');

            $url = 'https://api.github.com/repos/TodoPago/Plugin-Magento2/releases/latest';
            $curl->addHeader('User-Agent', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36');
            $curl->addHeader('Authorization', 'token 21600a0757d4b32418c54e3833dd9d47f78186b4');

            $curl->get($url);
            $response = $curl->getBody();
            $this->_logger->debug("Github - Respuesta: $response");

            $obResponse = json_decode($response);
            $versionInstalada = $obResponse->tag_name;
        } catch (\Exception $e) {
            //Error al conectar a Github
            $this->_logger->debug("Github - Error al conectar - Exeption: $e");
        }

        return $versionInstalada;
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
