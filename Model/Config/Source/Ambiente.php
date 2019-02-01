<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Model\Config\Source;

/**
 * Order Status source model.
 */
class Ambiente implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'test', 'label' => __('Developers')],
            ['value' => 'prod', 'label' => __('Produccion')],
        ];
    }
}
