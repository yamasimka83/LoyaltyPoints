<?php

namespace LoyaltyGroup\LoyaltyPoints\Observer;

use LoyaltyGroup\LoyaltyPoints\Api\Model\Total\LoyaltyPointsInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;

/**
 * Class SaveOrderBeforeSalesModelQuoteObserver
 * @package LoyaltyGroup\LoyaltyPoints\Observer
 */
class SaveOrderBeforeSalesModelQuoteObserver implements ObserverInterface
{
    /** {@inheritDoc} */
    public function execute(Observer $observer)
    {
        /* @var Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        foreach ([LoyaltyPointsInterface::CODE_AMOUNT, LoyaltyPointsInterface::BASE_CODE_AMOUNT] as $code) {
            if ($quote->hasData($code)) {
                $order->setData($code, $quote->getData($code));
            }
        }

        return $this;
    }
}
