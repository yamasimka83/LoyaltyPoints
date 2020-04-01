<?php

namespace LoyaltyGroup\LoyaltyPoints\Controller\Totals;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class Collect
 * @package LoyaltyGroup\LoyaltyPoints\Controller\Totals
 */
class Collect extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * Collect constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CustomerSession $customerSession
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CustomerSession $customerSession,
        CartRepositoryInterface $cartRepository
    ) {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Re-collect totals, to apply surcharge.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultJsonFactory->create();

        try {
            $this->_validateRequest();
            $isUse = $this->getRequest()->getParam('isUsePoints');
            $this->customerSession->setIsUse($isUse);
            $quoteId = $this->getRequest()->getParam('quoteId');
            $this->customerSession->setQuoteIdCheck($quoteId);
            $quote = $this->cartRepository->get($quoteId);
            $quote->collectTotals()->save();
            $result->setData($isUse);
        } catch (\Exception $exception) {
            return $result->setData($exception->getMessage());
        }

        return $result;
    }

    /**
     * Validates request.
     *
     * @return void
     *
     * @throws NotFoundException
     */
    protected function _validateRequest()
    {
        if (!$this->getRequest()->isAjax()) {
            throw new NotFoundException(__('Request type is incorrect'));
        }
    }
}
