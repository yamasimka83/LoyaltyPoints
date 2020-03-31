<?php

namespace LoyaltyGroup\LoyaltyPoints\Block\Order;

use LoyaltyGroup\LoyaltyPoints\Api\Builder\LoyaltyPointsBuilderInterface;
use LoyaltyGroup\LoyaltyPoints\Api\Model\Total\LoyaltyPointsInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Block\Order\Totals;

/**
 * Class LoyaltyPoints
 * @package LoyaltyGroup\LoyaltyPoints\Block\Order
 */
class LoyaltyPoints extends AbstractBlock
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

    /**
     * @return void
     */
    public function initTotals()
    {
        /** @var Totals $orderTotalsBlock */
        $orderTotalsBlock = $this->getParentBlock();
        /** @var Order $order */
        $order = $orderTotalsBlock->getOrder();
        if (!empty($order->getData(LoyaltyPointsInterface::CODE_AMOUNT))) {
            $data = $this->builder
                 ->setLabel(LoyaltyPointsInterface::LABEL)
                 ->setCode(LoyaltyPointsInterface::CODE)
                 ->setValue($order->getData(LoyaltyPointsInterface::CODE_AMOUNT))
                 ->setBaseValue($order->getData(LoyaltyPointsInterface::BASE_CODE_AMOUNT))
                 ->build();

            $orderTotalsBlock->addTotal($data, 'subtotal');
        }
    }
}
