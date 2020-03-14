<?php
/**
 * GetIdFromUrl
 *
 * @copyright Copyright © 2020  . All rights reserved.
 * @author    yamasimka83@gmail.com
 */

namespace LoyaltyGroup\LoyaltyPoints\Block;


use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Request\Http;


class GetIdFromUrl extends Template
{
    protected $request;

    public function __construct(Context $context, Http $request, array $data = [])
    {
        $this->request = $request;
        parent::__construct($context, $data);
    }

    public function getIdFromUrl()
    {
        $id = $this->request->getParam('id');
        return $id;
    }

}
