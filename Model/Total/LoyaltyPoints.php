<?php

namespace LoyaltyGroup\LoyaltyPoints\Model\Total;

use LoyaltyGroup\LoyaltyPoints\Api\Model\Total\LoyaltyPointsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;



/**
 * Class LoyaltyPoints
 * @package LoyaltyGroup\LoyaltyPoints\Model\Total
 */
class LoyaltyPoints extends AbstractTotal implements LoyaltyPointsInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Session
     */
    private $session;

    public function __construct
    (
        CustomerRepositoryInterface $customerRepository,
        Session $session
    )
    {
        $this->customerRepository = $customerRepository;
        $this->session = $session;
    }

    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        $items = $shippingAssignment->getItems();
        if (empty($items)) {
            return $this;
        }

        if ($this->session->isLoggedIn()) {

            $isUse = !empty($this->session->getIsUse()) ? $this->session->getIsUse() : false;

            if ($isUse == 'true') {

                $allTotalAmounts = array_sum($total->getAllTotalAmounts());
                $allBaseTotalAmounts = array_sum($total->getAllBaseTotalAmounts());

                $user = $this->customerRepository->getById($this->session->getCustomerId());

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
        if($this->session->isLoggedIn()) {
            $user = $this->customerRepository->getById($this->session->getCustomerId());
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
