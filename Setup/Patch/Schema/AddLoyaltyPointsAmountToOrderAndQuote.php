<?php

namespace LoyaltyGroup\LoyaltyPoints\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class AddLoyaltyPointsAmountToOrderAndQuote
 * @package LoyaltyGroup\LoyaltyPoints\Setup\Patch\Schema
 */
class AddLoyaltyPointsAmountToOrderAndQuote implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * EnableSegmentation constructor.
     *
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $columns = [
            'quote' => [
                'loyalty_points_amount' => [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default'  => 0.0,
                    'comment'  => 'Loyalty Points'
                ],
                'base_loyalty_points_amount'=> [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default'  => 0.0,
                    'comment'  => 'Base Loyalty Points'
                ]
            ],
            'quote_address' => [
                'loyalty_points_amount'=> [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default'  => 0.0,
                    'comment'  => 'Loyalty Points'
                ],
                'base_loyalty_points_amount' => [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default'  => 0.0,
                    'comment'  => 'Base Loyalty Points'
                ]
            ],
            'sales_order' => [
                'loyalty_points_amount'=> [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default'  => 0.0,
                    'comment'  => 'Loyalty Points'
                ],
                'base_loyalty_points_amount' => [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default'  => 0.0,
                    'comment'  => 'Base Loyalty Points'
                ]
            ],
        ];

        $connection = $this->schemaSetup->getConnection();

        foreach ($columns as $tableName => $columnData) {
            foreach ($columnData as $columnName => $definition) {
                $connection->addColumn(
                    $connection->getTableName($tableName),
                    $columnName,
                    $definition
                );
            }
        }
    }
}
