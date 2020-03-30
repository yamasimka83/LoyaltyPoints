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
use Magento\Framework\Stdlib\CookieManagerInterface;



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

    /**
     * @var CookieManagerInterface
     */
    private $cookie;

    private static $isUsePoints = false;

    public function __construct
    (
        CustomerRepositoryInterface $customerRepository,
        Session $session,
        CookieManagerInterface $cookie
    )
    {
        $this->customerRepository = $customerRepository;
        $this->session = $session;
        $this->cookie = $cookie;
    }

    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if ($this->session->isLoggedIn()) {
            parent::collect($quote, $shippingAssignment, $total);

            $isUse = !empty($this->session->getIsUse()) ? $this->session->getIsUse() : false;

            if ($isUse == 'true') {

                $allTotalAmounts = array_sum($total->getAllTotalAmounts());
                $allBaseTotalAmounts = array_sum($total->getAllBaseTotalAmounts());

                $user = $this->customerRepository->getById($this->session->getCustomerId());
                $points = $user->getCustomAttribute('loyalty_points')->getValue();

                $totalSale = $points >= $allTotalAmounts ? -($allTotalAmounts - 0.01) : -$points;
                $totalBaseSale = $points >= $allBaseTotalAmounts ? -($allBaseTotalAmounts - 0.01) : -$points;

                $total->addTotalAmount($this->getCode(), $totalSale);
                $total->addBaseTotalAmount($this->getCode(), $totalBaseSale);
                $total->setCustom($totalSale);
                $total->setBaseCustom($totalBaseSale);
                $quote->setData(self::CODE_AMOUNT, $totalSale);
                $quote->setData(self::BASE_CODE_AMOUNT, $totalBaseSale);
            } else {
                $total->setTotalAmount($this->getCode(), 0);
                $total->setBaseTotalAmount($this->getCode(), 0);
                $total->setCustom(0);
                $total->setBaseCustom(0);
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
        $loyaltyPointAmount = $total->getData(self::CODE_AMOUNT);
        $loyaltyPointAll = round($this->customerRepository->getById($this->session->getCustomerId())->getCustomAttribute('loyalty_points')->getValue(), 2);
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
