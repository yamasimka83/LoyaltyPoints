<?php

namespace LoyaltyGroup\LoyaltyPoints\Api\Model\Total;

use Magento\Quote\Model\Quote\Address\Total\CollectorInterface;
use Magento\Quote\Model\Quote\Address\Total\ReaderInterface;

/**
 * Interface CustomTotalInterface
 * @package LoyaltyGroup\LoyaltyPoints\Api\Model\Total
 */
interface LoyaltyPointsInterface extends CollectorInterface, ReaderInterface
{
    /**
     * @+
     * @const string
     */
    const CODE              = 'loyalty_points';
    const CODE_AMOUNT       = 'loyalty_points_amount';
    const BASE_CODE_AMOUNT  = 'base_loyalty_points_amount';
    const LABEL             = 'Loyalty Points';
}
