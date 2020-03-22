<?php
namespace LoyaltyGroup\LoyaltyPoints\Builder;

use LoyaltyGroup\LoyaltyPoints\Api\Builder\LoyaltyPointsBuilderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Phrase;

/**
 * Class LoyaltyPointsBuilder
 * @package LoyaltyGroup\LoyaltyPoints\Builder
 */
class LoyaltyPointsBuilder implements LoyaltyPointsBuilderInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $label;

    /**
     * @var float
     */
    private $value;

    /**
     * @var float
     */
    private $baseValue;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * LoyaltyPointsBuilder constructor.
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(DataObjectFactory $dataObjectFactory)
    {
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @inheritDoc
     */
    public function build(): DataObject
    {
        $this->validate();

        return $this->dataObjectFactory
                    ->create(
                        [
                            'data' => [
                                'code'       => $this->code,
                                'value'      => $this->value,
                                'base_value' => $this->baseValue,
                                'label'      => $this->label
                            ]
                        ]
                    );
    }

    /**
     * @inheritDoc
     */
    public function setCode(string $code): LoyaltyPointsBuilderInterface
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue(float $value): LoyaltyPointsBuilderInterface
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBaseValue(float $baseValue): LoyaltyPointsBuilderInterface
    {
        $this->baseValue = $baseValue;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLabel(string $label): LoyaltyPointsBuilderInterface
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @throws \LogicException
     */
    private function validate()
    {
        $prop = get_object_vars($this);

        foreach ($prop as $name => $value) {
            if ($value === null) {
                throw new \LogicException(sprintf("property %s is not initialized", $name));
            }
        }
    }
}
