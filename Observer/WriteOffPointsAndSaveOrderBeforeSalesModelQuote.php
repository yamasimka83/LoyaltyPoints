<?php

namespace LoyaltyGroup\LoyaltyPoints\Observer;

use LoyaltyGroup\LoyaltyPoints\Api\Model\Quote\LoyaltyPointsInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;


/**
 * Class WriteOffPointsAndSaveOrderBeforeSalesModelQuote
 * @package LoyaltyGroup\LoyaltyPoints\Observer
 */
class WriteOffPointsAndSaveOrderBeforeSalesModelQuote implements ObserverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    public function __construct
    (
        Session $session,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->session = $session;
        $this->customerRepository = $customerRepository;
    }

    /** {@inheritDoc} */
    public function execute(Observer $observer)
    {
        /* @var Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        /**
         * Save order.
         */
        foreach ([LoyaltyPointsInterface::CODE_AMOUNT, LoyaltyPointsInterface::BASE_CODE_AMOUNT] as $code) {
            if ($quote->hasData($code)) {
                $order->setData($code, $quote->getData($code));
            }
        }
        /**
         * Write-off points when using them.
         */
        if ($this->session->isLoggedIn() && $quote->hasData(LoyaltyPointsInterface::CODE_AMOUNT)) {
            $id = $this->session->getCustomerId();
            $user = $this->customerRepository->getById($id);

            $oldPoints = $user->getCustomAttribute(LoyaltyPointsInterface::CODE)->getValue();
            $newPoints =  $oldPoints - $quote->getData(LoyaltyPointsInterface::CODE_AMOUNT);

            $user->setCustomAttribute(LoyaltyPointsInterface::CODE, $newPoints);
            $this->customerRepository->save($user);
        }

        return $this;
    }
}
