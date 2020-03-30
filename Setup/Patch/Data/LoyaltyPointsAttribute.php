<?php

namespace LoyaltyGroup\LoyaltyPoints\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResourceModel;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Zend_Validate_Exception;

/**
 * Class LoyaltyPointsAttribute
 * @package LoyaltyGroup\LoyaltyPoints\Setup\Patch\Data
 */
class LoyaltyPointsAttribute implements DataPatchInterface
{
    const CODE_FIELD_NAME = 'loyalty_points';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributeResourceModel
     */
    private $attributeResourceModel;

    /**
     * LoyaltyPointsAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeResourceModel $attributeResourceModel
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        EavSetupFactory $eavSetupFactory,
        AttributeResourceModel $attributeResourceModel
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeResourceModel = $attributeResourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(Customer::ENTITY, self::CODE_FIELD_NAME);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            static::REFERRAL_CODE_FIELD_NAME,
            [
                'type' => 'decimal',
                'label' => 'loyalty_points',
                'input' => 'text',
                'required' => false,
                'sort_order' => 999,
                'position' => 999,
                'visible' => true,
                'default' => 0,
                'user_defined' => true,
                'system' => 0,
                'unique' => true,
            ]
        );

        $referralCodeAttribute = $customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, self::CODE_FIELD_NAME)
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_checkout', 'adminhtml_customer'],
            ]);

        $this->attributeResourceModel->save($referralCodeAttribute);
    }

}
