<?php

namespace Troyantec\Paymentez\Block\Adminhtml;

use Magento\Backend\Block\Admin\Formkey;

class Credential extends Formkey
{
    protected function _prepareLayout()
    {
        $this->setErrors(array());

        return parent::_prepareLayout();
    }
}
