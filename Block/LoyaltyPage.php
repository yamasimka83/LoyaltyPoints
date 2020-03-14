<?php

namespace LoyaltyGroup\LoyaltyPoints\Block;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use LoyaltyGroup\LoyaltyPoints\Api\Repository\UserRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use LoyaltyGroup\LoyaltyPoints\Model\CustomerSession;
use LoyaltyGroup\LoyaltyPoints\Model\CustomerSessionFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Encryption\EncryptorInterface;


class LoyaltyPage extends Template
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    private $encryptor;

    protected $request;

    private $repository;

    private $searchCriteriaBuilder;

    private $customerSession;

    private $customerSessionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    const XML_PATH_COUNT_LOYALTY = 'loyaltyPoints/general/cashback';

    public function __construct(
        EncryptorInterface $encryptor,
        Http $request,
        ScopeConfigInterface $scopeConfig,
        Context $context,
        UserRepositoryInterface $repository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerSession $customerSession,
        CustomerSessionFactory $customerSessionFactory,
        SerializerInterface $serializer,

        array $data = []
    ) {
        $this->encryptor = $encryptor;
        $this->request = $request;
        $this->repository = $repository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->serializer = $serializer;
    }

    /**
     * Sample function returning config value
     * @param string|null $storeId
     * @return mixed
     */

    public function getPercentOfLoyalty(?string $storeId = null)
    {
        $persentOfLoyalty = $this->scopeConfig->getValue(
            self::XML_PATH_COUNT_LOYALTY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $persentOfLoyalty;
    }

    public function getCustomerId()
    {
        $_customerSession = $this->customerSessionFactory->create();
        $customerID = $_customerSession->getLoggedinCustomerId();
        return $customerID;
    }

    public function getCustomerUrl()
    {
        $_customerSession = $this->customerSessionFactory->create();
        $url = $_customerSession->getBaseUrlOfMyStore();
        return $url;
    }

    public function getPointsById()
    {
        $user = $this->repository->getById($this->getCustomerId());
        return $user->getPoints();
    }

    public function createReferralLink()
    {
        $link = $this->getCustomerUrl();
        /** @TODO fix encrypt */
        $encrypt = $this->encryptor->encrypt($this->getCustomerId());
        $link .= '?ref=';
        $link .= $encrypt;
        return $link;
    }

    public function getJsonConfig()
    {
        $userInfo = [];

        $userInfo['points'] = $this->getPointsById();
        $userInfo['percentOfLoyalty'] = $this->getPercentOfLoyalty();
        $userInfo['referralLink'] = $this->createReferralLink();

        return $this->serializer->serialize($userInfo);
    }
    public function getIdFromUrl()
    {
        $id = $this->request->getParam('id');
        return $id;
    }
    public function showReferralId()
    {   $this->customerSession->setReferralId($this->getIdFromUrl());
        return $this->customerSession->getReferralId();
    }
}
