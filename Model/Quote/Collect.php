<?php

namespace LoyaltyGroup\LoyaltyPoints\Model\Quote;

use LoyaltyGroup\LoyaltyPoints\Api\Model\Quote\LoyaltyPointsInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\ScopeInterface;


/**
 * Class LoyaltyPoints
 * @package LoyaltyGroup\LoyaltyPoints\Model\Quote
 */
class Collect extends AbstractTotal implements LoyaltyPointsInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @+
     * @const string
     */
    const XML_PATH_MIN_TOTAL = 'loyaltyPoints/general/loyalty_points_minimum_total';

    public function __construct
    (
        CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
    }

    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        $items = $shippingAssignment->getItems();
        if (empty($items)) {
            return $this;
        }

        if ($this->customerSession->isLoggedIn()) {

            $isUse = !empty($this->customerSession->getIsUse()) ? $this->customerSession->getIsUse() : false;

            if ($isUse == 'true') {

                $allTotalAmounts = array_sum($total->getAllTotalAmounts());
                $allBaseTotalAmounts = array_sum($total->getAllBaseTotalAmounts());

                $user = $this->customerRepository->getById($this->customerSession->getCustomerId());

                if(empty($user->getCustomAttribute(self::CODE))) {
                    $user->setCustomAttribute(self::CODE, 0);
                    $this->customerRepository->save($user);
                }

                $points = round($user->getCustomAttribute(self::CODE)->getValue());

                $minimumTotal = $this->scopeConfig->getValue
                (
                    self::XML_PATH_MIN_TOTAL,
                    ScopeInterface::SCOPE_STORE
                );

                $totalSale = -($points >= $allTotalAmounts ? ($allTotalAmounts - $minimumTotal) : $points);
                $totalBaseSale = -($points >= $allBaseTotalAmounts ? ($allBaseTotalAmounts - $minimumTotal) : $points);

                $total->addTotalAmount($this->getCode(), $totalSale);
                $total->addBaseTotalAmount($this->getCode(), $totalBaseSale);

                $quote->setData(self::CODE_AMOUNT, $totalSale);
                $quote->setData(self::BASE_CODE_AMOUNT, $totalBaseSale);
            } else {
                $total->setTotalAmount($this->getCode(), 0);
                $total->setBaseTotalAmount($this->getCode(), 0);

                $quote->setData(self::CODE_AMOUNT, 0);
                $quote->setData(self::BASE_CODE_AMOUNT, 0);
            }
            return $this;
        }
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function fetch(Quote $quote, Total $total)
    {
        $loyaltyPointAll = 0;
        $loyaltyPointAmount = 0;
        if($this->customerSession->isLoggedIn()) {
            $user = $this->customerRepository->getById($this->customerSession->getCustomerId());
            if(empty($user->getCustomAttribute(self::CODE))) {
                $user->setCustomAttribute(self::CODE, 0);
                $this->customerRepository->save($user);
            }
            $loyaltyPointAmount = round($total->getData(self::CODE_AMOUNT));
            $loyaltyPointAll = round($user->getCustomAttribute(self::CODE)->getValue());

        }
        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => [$loyaltyPointAmount, $loyaltyPointAll]
        ];
    }

    public function getLabel()
    {
        return __(self::LABEL);
    }


}
