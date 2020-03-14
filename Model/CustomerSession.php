<?php

namespace LoyaltyGroup\LoyaltyPoints\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class CustomerSession
{
    /**
     * @var Session
     */
    protected $session;

    protected $_storeManager;

    /**
     * CustomerSession constructor.
     * @param StoreManagerInterface $storeManager
     * @param SessionFactory $customerSession
     * @param Session $session
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Session $session
    ) {
        $this->_storeManager = $storeManager;
        $this->session = $session;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     *
     */
    public function getBaseUrlOfMyStore()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    /**
     * @return mixed
     */
    public function getLoggedinCustomerId() {
        if ($this->session->isLoggedIn()) {
            return $this->session->getCustomerId();;
        }
        return "Not Logged In!";
    }
    public function setReferralId($refId)
    {
        $this->session->setData('referralId', $refId);
    }
    public function getReferralId()
    {
        return $this->session->getData('referralId');
    }
}
