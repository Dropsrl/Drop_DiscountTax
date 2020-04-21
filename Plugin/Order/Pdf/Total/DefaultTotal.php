<?php

namespace Drop\DiscountTax\Plugin\Order\Pdf\Total;

class DefaultTotal
{

    /**
     * @var \Drop\DiscountTax\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Drop\DiscountTax\Helper\Data $helperData,
     */
    public function __construct(
        \Drop\DiscountTax\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    public function afterGetTotalsForDisplay($subject, $result)
    {
        if($subject->getSourceField() != 'discount_amount') {
            return $result;
        }

        $taxRatePercent = $this->helperData->getTaxRate($subject->getSource());
        if(!$taxRatePercent) {
            return $result;
        }

        if($this->helperData->getSalesDisplayDiscountBoth()) {
            $result[1] = $result[0];
            $result[1]['amount'] = $subject->getOrder()->formatPriceTxt($this->helperData->getValueExcludingTax((float) $result[0]['amount'], $taxRatePercent));
            $result[1]['label'] = str_replace(':', '', $result[0]['label'] . ' (' . __('Excl. Tax') . ')') . ':';
        } elseif($this->helperData->getSalesDisplayDiscountExcludingTax()) {
            $result[0]['amount'] = $subject->getOrder()->formatPriceTxt($this->helperData->getValueExcludingTax((float) $result[0]['amount'], $taxRatePercent));
        }
        $result[0]['label'] = str_replace(':', '', $this->helperData->getDiscountLabel($result[0]['label'])) . ':';

        return $result;
    }

}
