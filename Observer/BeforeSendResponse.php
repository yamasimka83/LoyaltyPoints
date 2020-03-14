<?php

namespace LoyaltyGroup\LoyaltyPoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Encryption\EncryptorInterface;


class BeforeSendResponse implements ObserverInterface
{
    private $session;

    private $encryptor;

    public function __construct(Session $session, EncryptorInterface $encryptor)
    {
        $this->session = $session;
        $this->encryptor = $encryptor;
    }

    public function execute(Observer $observer)
    {
        if(!empty($observer->getEvent()->getData('request')->getParam('ref'))) {
            $decrypt = $observer->getEvent()->getData('request')->getParam('ref');
            $id = $this->encryptor->decrypt($decrypt);
            if($id != $this->session->getCustomerId()) {
                $this->session->setReferralId($id);
            }
        }
    }
}
