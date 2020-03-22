<?php

namespace LoyaltyGroup\LoyaltyPoints\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class LoyaltyPage extends Template
{
    /**
     * @var Session
     */
    private $session;

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
     * @param Session $session
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        EncryptorInterface $encryptor,
        Session $session,
        Context $context,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->encryptor = $encryptor;
        $this->storeManager = $storeManager;
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get loyalty points by user id.
     *
     * @return mixed
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getLoyaltyPoints()
    {
        $id = $this->session->getCustomerId();
        $user = $this->customerRepository->getById($id);
        return $user->getCustomAttribute('loyalty_points')->getValue();
    }

    /**
     * Dynamic create a personal referral link.
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function createReferralLink()
    {
        $url = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
        $encrypt = urlencode($this->encryptor->encrypt($this->session->getCustomerId()));
        $link = $url . "?ref=" . $encrypt;
        return $link;
    }
}
