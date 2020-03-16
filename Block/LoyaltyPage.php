<?php

namespace LoyaltyGroup\LoyaltyPoints\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use LoyaltyGroup\LoyaltyPoints\Api\Repository\UserRepositoryInterface;

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
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * LoyaltyPage constructor.
     *
     * @param EncryptorInterface $encryptor
     * @param Session $session
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param UserRepositoryInterface $repository
     * @param array $data
     */
    public function __construct(
        EncryptorInterface $encryptor,
        Session $session,
        Context $context,
        StoreManagerInterface $storeManager,
        UserRepositoryInterface $repository,
        array $data = []
    ) {
        $this->encryptor = $encryptor;
        $this->storeManager = $storeManager;
        $this->session = $session;
        $this->repository = $repository;
        parent::__construct($context, $data);
    }

    /**
     * Get loyalty points by user id.
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getPointsById()
    {
        $id = $this->session->getCustomerId();
        $user = $this->repository->getById($id);
        return $user->getPoints();
    }

    /**
     * Dynamic create a personal referral link.
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function createReferralLink()
    {
        /** @TODO fix encrypt */

        $url = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
        $encrypt = $this->encryptor->encrypt($this->session->getCustomerId());
        $link = $url . "?ref=" . $encrypt;
        return $link;
    }
}
