<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Model\Config\Source\ControlFraude;

/**
 * Order Status source model.
 */
class Vertical implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'retail', 'label' => __('Retail')],
            ['value' => 'services', 'label' => __('Services')],
            ['value' => 'digitalgoods', 'label' => __('Digital Goods')],
            ['value' => 'ticketing', 'label' => __('Ticketing')],
        ];
    }
}
