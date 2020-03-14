<?php

namespace LoyaltyGroup\LoyaltyPoints\Block\Order;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\AbstractBlock;

class CustomTotal extends AbstractBlock
{
    public function initTotals()
    {
        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getOrder();
        if ($order->getCustomAmount() > 0) {
            $orderTotalsBlock->addTotal(new DataObject([
                'code' => 'loyalty_points',
                'label' => __('Loyalty Points'),
                'value' => $order->getCustomAmount(),
                'base_value' => $order->getCustomBaseAmount(),
            ]), 'subtotal');
        }
    }
}
