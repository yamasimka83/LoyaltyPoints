<?php

namespace LoyaltyGroup\LoyaltyPoints\Observer;

use LoyaltyGroup\LoyaltyPoints\Api\Model\Quote\LoyaltyPointsInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote;

/**
 * Class AddLoyaltyPointsAfterPlaceOrder
 * @package LoyaltyGroup\LoyaltyPoints\Observer
 */
class AddLoyaltyPointsAfterPlaceOrder implements ObserverInterface
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

    /**
     * @var Quote
     */
    private $quote;

    /**
     * @+
     * @const string
     */
    const XML_PATH_COUNT_LOYALTY = 'loyaltyPoints/general/loyalty_points_percent';

    /**
     * AfterPlaceOrder constructor.
     * @param Session $session
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerRepositoryInterface $customerRepository
     * @param Quote $quote
     */
    public function __construct(
        Session $session,
        ScopeConfigInterface $scopeConfig,
        CustomerRepositoryInterface $customerRepository,
        Quote $quote
    ) {
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
        $this->quote = $quote;
    }

    /**
     * Adds points to the referral.
     *
     * @param Observer $observer
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputMismatchException
     */
    public function execute(Observer $observer)
    {
        /**
         * Adds points to the referral.
         */
        if(!empty($this->session->getReferralId())) {

            $item = $observer->getEvent()->getOrder()->getBaseTotalDue();

            $percent = $this->scopeConfig->getValue
            (
                self::XML_PATH_COUNT_LOYALTY,
                ScopeInterface::SCOPE_STORE
            );

            $referralId = $this->session->getReferralId();
            $user = $this->customerRepository->getById($referralId);

            $oldPoints = $this->customerRepository->getById($referralId)->getCustomAttribute(LoyaltyPointsInterface::CODE)->getValue();
            $newPoints = $oldPoints + round(($item * $percent / 100));

            $user->setCustomAttribute(LoyaltyPointsInterface::CODE, $newPoints);
            $this->customerRepository->save($user);
        }
    }
}
