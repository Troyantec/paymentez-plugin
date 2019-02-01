<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Troyantec\Paymentez\Model\Config\Source;

/**
 * Order Status source model.
 */
class Cuotas implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1',  'label' => __('01')],
            ['value' => '2',  'label' => __('02')],
            ['value' => '3',  'label' => __('03')],
            ['value' => '4',  'label' => __('04')],
            ['value' => '5',  'label' => __('05')],
            ['value' => '6',  'label' => __('06')],
            ['value' => '7',  'label' => __('07')],
            ['value' => '8',  'label' => __('08')],
            ['value' => '9',  'label' => __('09')],
            ['value' => '10', 'label' => __('10')],
            ['value' => '11', 'label' => __('11')],
            ['value' => '12', 'label' => __('12')],
        ];
    }
}
