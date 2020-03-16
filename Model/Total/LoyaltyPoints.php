<?php

Namespace LoyaltyGroup\LoyaltyPoints\Model\Total;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote;
use LoyaltyGroup\LoyaltyPoints\Api\Repository\UserRepositoryInterface;
use Magento\Customer\Model\Session;

class LoyaltyPoints extends AbstractTotal
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * int $points
     */
    private $points;

    /**
     * LoyaltyPoints constructor.
     * @param UserRepositoryInterface $repository
     * @param Session $session
     */
    public function __construct(
        UserRepositoryInterface $repository,
        Session $session
    ) {
        $this->repository = $repository;
        $this->session = $session;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return void
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
            parent::collect($quote, $shippingAssignment, $total);

//            $items = $shippingAssignment->getItems();
//            if (!count($items)) {
//                return $this;
//            }
            if($this->session->isLoggedIn()) {
                $id = $this->session->getCustomerId();
                $this->points = $this->repository->getById($id)->getPoints();
                $amount = $this->points;
                $allTotalAmounts = array_sum($total->getAllTotalAmounts());
                $allBaseTotalAmounts = array_sum($total->getAllBaseTotalAmounts());

                $totalSale = $amount > $allTotalAmounts ? ($allTotalAmounts - 0.01) : $amount;
                $totalBaseSale = $amount > $allBaseTotalAmounts ? ($allBaseTotalAmounts - 0.01) : $amount;

                $total->addTotalAmount('loyalty_points', -$totalSale);
                $total->addBaseTotalAmount('loyalty_points', -$totalBaseSale);

//                return $this;
            }
    }

    /**
     * @param Total $total
     */
    protected function clearValues(Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        if($this->session->isLoggedIn()) {
            return [
                'code' => 'loyalty_points',
                'title' => 'loyalty_points',
                'value' => $this->points
            ];
        }
    }
}
