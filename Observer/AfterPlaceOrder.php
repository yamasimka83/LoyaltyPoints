<?php

namespace LoyaltyGroup\LoyaltyPoints\Observer;

use LoyaltyGroup\LoyaltyPoints\Repository\UserRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use LoyaltyGroup\LoyaltyPoints\Model\CustomerSession;
use Magento\Customer\Model\Session;

class AfterPlaceOrder implements ObserverInterface
{
    private $session;
    protected $scopeConfig;
    protected $repository;
    const XML_PATH_COUNT_LOYALTY = 'loyaltyPoints/general/cashback';

    public function __construct(
        Session $session,
        ScopeConfigInterface $scopeConfig,
        UserRepository $repository
    ) {
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        $this->repository = $repository;
    }

    public function execute(Observer $observer)
    {
        if(!empty($this->session->getReferralId())) {
            $item = $observer->getEvent()->getOrder()->getBaseTotalDue();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $percent = $this->scopeConfig->getValue(self::XML_PATH_COUNT_LOYALTY, $storeScope);
            /** @TODO fix round */
            $point = round($item * $percent / 100);
            $refId = $this->session->getReferralId();
            $oldPoints = $this->repository->getById($refId)->getPoints();
            $newPoints = $oldPoints + $point;
            $user = $this->repository->getById($refId);
            $user->setPoints($newPoints);
            $this->repository->save($user);
        }
        var_dump($this->session->getReferralId());
    }
}
