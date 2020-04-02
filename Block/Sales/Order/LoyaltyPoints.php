<?php

namespace LoyaltyGroup\LoyaltyPoints\Block\Sales\Order;

use LoyaltyGroup\LoyaltyPoints\Api\Builder\LoyaltyPointsBuilderInterface;
use LoyaltyGroup\LoyaltyPoints\Api\Model\Quote\LoyaltyPointsInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Block\Adminhtml\Order\Invoice\Totals;

/**
 * Class LoyaltyPoints
 * @package LoyaltyGroup\LoyaltyPoints\Block\Sales\Order
 */
class LoyaltyPoints extends Template
{
    /**
     * @var LoyaltyPointsBuilderInterface
     */
    private $builder;

    /**
     * Loyalty points constructor.
     *
     * @param Context                       $context
     * @param LoyaltyPointsBuilderInterface $loyaltyPointsBuilder
     * @param array                         $data
     */
    public function __construct(
        Context $context,
        LoyaltyPointsBuilderInterface $loyaltyPointsBuilder,
        array $data = []
    ) {
        $this->builder = $loyaltyPointsBuilder;
        parent::__construct($context, $data);
    }

    public function initTotals()
    {
        /** @var $parent Totals */
        $parent = $this->getParentBlock();
        $source = $parent->getSource();

        if (!empty($source->getData(LoyaltyPointsInterface::CODE_AMOUNT))) {
            $loyaltyPointsData = $this->getLoyaltyPointsData(
                $source->getData(LoyaltyPointsInterface::CODE_AMOUNT),
                $source->getData(LoyaltyPointsInterface::BASE_CODE_AMOUNT)
            );

            $parent->addTotal($loyaltyPointsData, 'subtotal');
        }

        return $this;
    }

    /**
     * @param $total
     * @param $baseTotal
     * @return DataObject
     */
    private function getLoyaltyPointsData($total, $baseTotal) : DataObject
    {
        return $this->builder
            ->setCode(LoyaltyPointsInterface::CODE)
            ->setValue($total)
            ->setBaseValue($baseTotal)
            ->setLabel(LoyaltyPointsInterface::LABEL)
            ->build();
    }
}
