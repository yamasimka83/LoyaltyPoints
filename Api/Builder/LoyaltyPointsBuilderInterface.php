<?php
namespace LoyaltyGroup\LoyaltyPoints\Api\Builder;

use Magento\Framework\DataObject;
use Magento\Framework\Phrase;

/**
 * Interface LoyaltyPointsBuilderInterface
 * @package ALevel\CustomTotal\Api\Builder
 */
interface LoyaltyPointsBuilderInterface
{
    /**
     * @return DataObject
     */
    public function build() : DataObject;

    /**
     * @param string $code
     * @return LoyaltyPointsBuilderInterface
     */
    public function setCode(string $code) : LoyaltyPointsBuilderInterface;

    /**
     * @param float $value
     * @return LoyaltyPointsBuilderInterface
     */
    public function setValue(float $value) : LoyaltyPointsBuilderInterface;

    /**
     * @param float $baseValue
     * @return LoyaltyPointsBuilderInterface
     */
    public function setBaseValue(float $baseValue) : LoyaltyPointsBuilderInterface;

    /**
     * @param string $label
     * @return LoyaltyPointsBuilderInterface
     */
    public function setLabel(string $label) : LoyaltyPointsBuilderInterface;
}
