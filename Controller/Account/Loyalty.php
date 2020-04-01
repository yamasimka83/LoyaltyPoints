<?php

namespace LoyaltyGroup\LoyaltyPoints\Controller\Account;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Loyalty
 * @package LoyaltyGroup\LoyaltyPoints\Controller\Account
 */
class Loyalty extends AbstractAccount
{
    /**
     * Render Loyalty Points page in customer account.
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
