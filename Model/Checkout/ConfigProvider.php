<?php

namespace LoyaltyGroup\LoyaltyPoints\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;

/**
 * Class ConfigProvider
 *
 * @package LoyaltyGroup\LoyaltyPoints\Model\Checkout
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ConfigProvider constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Provide configuration action path.
     *
     * @return array
     */
    public function getConfig() : array
    {
        return [
            'collectTotalsPath' => $this->getRecollectTotalsPath()
        ];
    }

    /**
     * Get action path for re-collect totals.
     *
     * @return string
     */
    private function getRecollectTotalsPath(): string
    {
        return $this->urlBuilder->getUrl('customer/totals/collect');
    }
}
