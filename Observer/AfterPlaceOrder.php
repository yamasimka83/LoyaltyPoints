<?php

namespace LoyaltyGroup\LoyaltyPoints\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\ScopeInterface;

class AfterPlaceOrder implements ObserverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    const XML_PATH_COUNT_LOYALTY = 'loyaltyPoints/general/cashback';

    public function __construct(
        Session $session,
        ScopeConfigInterface $scopeConfig,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
    }

    public function execute(Observer $observer)
    {
        if(!empty($this->session->getReferralId())) {
            $item = $observer->getEvent()->getOrder()->getBaseTotalDue();
            $storeScope = ScopeInterface::SCOPE_STORE;
            $percent = $this->scopeConfig->getValue(self::XML_PATH_COUNT_LOYALTY, $storeScope);
            /** @TODO fix round */
            $point = round(($item * $percent / 100), 2);
            $refId = $this->session->getReferralId();
            $oldPoints = $this->customerRepository->getById($refId)->getCustomAttribute('loyalty_points')->getValue();
            $newPoints = $oldPoints + $point;
            $user = $this->customerRepository->getById($refId);
            $user->setCustomAttribute('loyalty_points', $newPoints);
            $this->customerRepository->save($user);
        }
    }
}
