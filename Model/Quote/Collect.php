<?php

namespace LoyaltyGroup\LoyaltyPoints\Model\Quote;

use LoyaltyGroup\LoyaltyPoints\Api\Model\Quote\LoyaltyPointsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;



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

    public function __construct
    (
        CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
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

                if(empty($user->getCustomAttribute('loyalty_points'))) {
                    $user->setCustomAttribute('loyalty_points', 0);
                    $this->customerRepository->save($user);
                }

                $points = $user->getCustomAttribute('loyalty_points')->getValue();

                $totalSale = $points >= $allTotalAmounts ? ($allTotalAmounts - 0.01) : $points;
                $totalBaseSale = $points >= $allBaseTotalAmounts ? ($allBaseTotalAmounts - 0.01) : $points;

                $total->addTotalAmount($this->getCode(), -$totalSale);
                $total->addBaseTotalAmount($this->getCode(), -$totalBaseSale);

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
            $loyaltyPointAmount = $total->getData(self::CODE_AMOUNT);
            $loyaltyPointAll = round($user->getCustomAttribute('loyalty_points')->getValue(), 2);

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
