<?php

namespace LoyaltyGroup\LoyaltyPoints\Controller\Customer;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Loyalty extends AbstractAccount
{
    /**
     * Render posts.
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
