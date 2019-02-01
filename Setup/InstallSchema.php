<?php

namespace Troyantec\Paymentez\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('paymentez_transacciones')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'orderid',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'OrderId'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Status'
        )->addColumn(
            'payment_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Payment Date'
        )->addColumn(
            'dev_reference',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Dev Reference'
        )->addColumn(
            'transaction_reference',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Reference Transaction'
        )->addColumn(
            'authorizationkey',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'AuthorizationKey'
        )->setComment(
            'Paymentez Transacciones'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
