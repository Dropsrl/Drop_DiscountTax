<?php

namespace Drop\DiscountTax\Block\Sales;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject;

class DiscountTax extends Template
{

    /**
     * Source object
     *
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * @var \Drop\DiscountTax\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Drop\DiscountTax\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Drop\DiscountTax\Helper\Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_source = $parent->getSource();
        $discountTotal = $parent->getTotal('discount');
        if(!$discountTotal) {
            return $this;
        }

        $baseLabel = $discountTotal->getLabel();
        $value = $discountTotal->getValue();
        $baseValue = $discountTotal->getBaseValue();
        if(empty($value) || ($value == 0) || empty($baseValue) || ($baseValue == 0)) {
            return $this;
        }

        $taxRatePercent = $this->helperData->getTaxRate($this->_source);
        if(!$taxRatePercent) {
            return $this;
        }

        if($this->helperData->getSalesDisplayDiscountBoth()) {
            $discountExcludingTax = new DataObject(
                [
                    'code' => 'discount_excl_tax',
                    'strong' => false,
                    'value' => $this->helperData->getValueExcludingTax($value, $taxRatePercent),
                    'base_value' => $this->helperData->getValueExcludingTax($baseValue, $taxRatePercent),
                    'label' => $baseLabel . ' (' . __('Excl. Tax') . ')',
                ]
            );
            $parent->addTotalBefore($discountExcludingTax, 'discount');
        } elseif($this->helperData->getSalesDisplayDiscountExcludingTax()) {
            $discountTotal->setValue($this->helperData->getValueExcludingTax($value, $taxRatePercent));
            $discountTotal->setBaseValue($this->helperData->getValueExcludingTax($baseValue, $taxRatePercent));
        }

        $label = $this->helperData->getDiscountLabel($baseLabel);
        $discountTotal->setLabel($label);

        return $this;
    }

}
