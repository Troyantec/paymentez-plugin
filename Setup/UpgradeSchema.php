<?php

namespace Troyantec\Paymentez\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.6.0') < 0) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('Paymentez_direcciones_googemaps')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'id_customer',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'OrderId'
            )->addColumn(
                'sha1',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'sha1'
            )->addColumn(
                'billing_CSBTSTREET1',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'billing_CSBTSTREET1'
            )->addColumn(
                'billing_CSBTSTATE',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'billing_CSBTSTATE'
            )->addColumn(
                'billing_CSBTCITY',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'billing_CSBTCITY'
            )->addColumn(
                'billing_CSBTCOUNTRY',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'billing_CSBTCOUNTRY'
            )->addColumn(
                'billing_CSBTPOSTALCODE',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'billing_CSBTPOSTALCODE'
            )->addColumn(
                'shipping_CSSTSTREET1',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'shipping_CSSTSTREET1'
            )->addColumn(
                'shipping_CSSTSTATE',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'shipping_CSSTSTATE'
            )->addColumn(
                'shipping_CSSTCITY',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'shipping_CSSTCITY'
            )->addColumn(
                'shipping_CSSTCOUNTRY',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'shipping_CSSTCOUNTRY'
            )->addColumn(
                'shipping_CSSTPOSTALCODE',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'shipping_CSSTPOSTALCODE'
            )->setComment(
                'Paymentez Direcciones Googlemaps'
            );
            $setup->getConnection()->createTable($table);
        }
        $setup->endSetup();
    }
}
