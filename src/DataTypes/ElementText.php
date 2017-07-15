<?php

namespace Cheppers\GatherContent\DataTypes;

class ElementText extends Element
{
    /**
     * @var string
     */
    public $limitType = '';

    /**
     * @var int
     */
    public $limit = 0;

    /**
     * @var bool
     */
    public $plainText = false;

    /**
     * @var string
     */
    public $value = '';

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function initPropertyMapping()
    {
        $this->propertyMapping += [
            'limit_type' => 'limitType',
            'limit' => 'limit',
            'plain_text' => 'plainText',
            'value' => 'value',
        ];

        return parent::initPropertyMapping();
    }
}
