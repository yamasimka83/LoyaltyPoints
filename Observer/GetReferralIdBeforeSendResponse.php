<?php

namespace LoyaltyGroup\LoyaltyPoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class GetReferralIdBeforeSendResponse
 * @package LoyaltyGroup\LoyaltyPoints\Observer
 */
class GetReferralIdBeforeSendResponse implements ObserverInterface
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
     * BeforeSendResponse constructor.
     * @param Session $session
     * @param EncryptorInterface $encryptor
     */
    public function __construct(Session $session, EncryptorInterface $encryptor)
    {
        $this->session = $session;
        $this->encryptor = $encryptor;
    }

    /**
     * Decrypts referralId and stores it in a session.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if(!empty($observer->getEvent()->getData('request')->getParam('ref'))) {

            $decrypt = urldecode($observer->getEvent()->getData('request')->getParam('ref'));
            $id = $this->encryptor->decrypt($decrypt);

            /**
             * Check that the customerId is not equal to the referralId.
             */
            if($id != $this->session->getCustomerId()) {
                $this->session->setReferralId($id);
            }
        }
    }
}
