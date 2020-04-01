<?php

namespace LoyaltyGroup\LoyaltyPoints\Block;

use LoyaltyGroup\LoyaltyPoints\Api\Model\Quote\LoyaltyPointsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class LoyaltyPage
 * @package LoyaltyGroup\LoyaltyPoints\Block
 */
class LoyaltyPage extends Template
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * LoyaltyPage constructor.
     *
     * @param EncryptorInterface $encryptor
     * @param CustomerSession $customerSession
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        EncryptorInterface $encryptor,
        CustomerSession $customerSession,
        Context $context,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->encryptor = $encryptor;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get loyalty points by user id.
     *
     * @return int
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getLoyaltyPoints() : int
    {
        $id = $this->customerSession->getCustomerId();
        $user = $this->customerRepository->getById($id);

        if(empty($user->getCustomAttribute(LoyaltyPointsInterface::CODE))) {
            $user->setCustomAttribute(LoyaltyPointsInterface::CODE, 0);
            $this->customerRepository->save($user);
        }

        return round($user->getCustomAttribute(LoyaltyPointsInterface::CODE)->getValue());
    }

    /**
     * Dynamic create a personal referral link.
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function createReferralLink() : string
    {
        $url = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
        $encrypt = urlencode($this->encryptor->encrypt($this->customerSession->getCustomerId()));
        $link = $url . "?ref=" . $encrypt;
        return $link;
    }
}
