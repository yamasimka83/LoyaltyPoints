<?php

namespace LoyaltyGroup\LoyaltyPoints\Model\Total;

use LoyaltyGroup\LoyaltyPoints\Api\Model\Total\LoyaltyPointsInterface;
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

    public function __construct(CustomerRepositoryInterface $customerRepository, Session $session)
    {
        $this->customerRepository = $customerRepository;
        $this->session = $session;
    }

    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        $allTotalAmounts = array_sum($total->getAllTotalAmounts());
        $allBaseTotalAmounts = array_sum($total->getAllBaseTotalAmounts());

        $points = 0;

        if ($this->session->isLoggedIn()) {
            $user = $this->customerRepository->getById($this->session->getCustomerId());
            $points = $user->getCustomAttribute('loyalty_points')->getValue();

        }
        $totalSale = $points >= $allTotalAmounts ? ($allTotalAmounts - 0.01) : $points;
        $totalBaseSale = $points >= $allBaseTotalAmounts ? ($allBaseTotalAmounts - 0.01) : $points;

        $total->addTotalAmount($this->getCode(), -$totalSale);
        $total->addBaseTotalAmount($this->getCode(), -$totalBaseSale);
        $quote->setData(self::CODE_AMOUNT, $totalSale);
        $quote->setData(self::BASE_CODE_AMOUNT, $totalBaseSale);

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => [$total->getData(self::CODE_AMOUNT), round($this->customerRepository->getById($this->session->getCustomerId())->getCustomAttribute('loyalty_points')->getValue(), 2)]
        ];
    }

    public function getLabel()
    {
        return __(self::LABEL);
    }
}
