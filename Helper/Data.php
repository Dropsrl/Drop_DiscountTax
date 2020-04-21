<?php

namespace Drop\DiscountTax\Helper;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const EXCLUDING_TAX = 1;
    const INCLUDING_TAX = 2;
    const BOTH_TAX = 3;

    const XML_PATH_TAX_SALES_DISPLAY_DISCOUNT = 'tax/sales_display/discount';

    /**
     * \Magento\Framework\App\Config\ScopeConfigInterface
     *
     * @var type
     */
    protected $scopeConfig;

    /**
     * \Magento\Sales\Model\ResourceModel\Order\Tax\Collection
     *
     * @var type
     */
    protected $taxCollection;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\ResourceModel\Order\Tax\Collection $taxCollection
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->taxCollection = $taxCollection;
    }

    /**
     * Get Admin discount tax confg
     * @return mixed
     */
    public function getSalesDisplayDiscount()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TAX_SALES_DISPLAY_DISCOUNT);
    }

    /**
     * True if excluding
     * @return bool
     */
    public function getSalesDisplayDiscountExcludingTax()
    {
        if(empty($this->getSalesDisplayDiscount()) || ($this->getSalesDisplayDiscount() == self::EXCLUDING_TAX)) {
            return true;
        }
        return false;
    }

    /**
     * True if including
     * @return bool
     */
    public function getSalesDisplayDiscountIncludingTax()
    {
        if($this->getSalesDisplayDiscount() == self::INCLUDING_TAX) {
            return true;
        }
        return false;
    }

    /**
     * True if both
     * @return bool
     */
    public function getSalesDisplayDiscountBoth()
    {
        if($this->getSalesDisplayDiscount() == self::BOTH_TAX) {
            return true;
        }
        return false;
    }

    /**
     * Add incl/excl tax string based on configuration
     * @param $label
     * @return string
     */
    public function getDiscountLabel($label)
    {
        if($this->getSalesDisplayDiscountIncludingTax() || $this->getSalesDisplayDiscountBoth()){
            return $label .= ' (' . __('Incl. Tax') . ')';
        }
        return $label .= ' (' . __('Excl. Tax') . ')';
    }

    /**
     * Calc tax rate by order/invoice/creditmemo
     * @param $source
     * @return bool|int
     * @throws \Exception
     */
    public function getTaxRate($source)
    {
        if ($source instanceof InvoiceInterface) {
            $order = $source->getOrder();
        } elseif ($source instanceof CreditMemoInterface) {
            $order = $source->getOrder();
        } elseif ($source instanceof OrderInterface) {
            $order = $source;
        } else {
            throw new \Exception('Cannot get order.');
        }

        $taxRates = $this->taxCollection->loadByOrder($order)->getFirstItem();
        if(!$taxRates || !$taxRates->getTaxId()) {

            if($order->getPayment()->getMethod() == 'free') {
                //Free orders are not written on sales_order_tax.
                foreach($order->getItems() as $item) {
                    return (int) $item->getTaxPercent();
                }
            }

            return false;
        }

        $taxRatePercent = (int) $taxRates->getPercent();
        if(empty($taxRatePercent)) {
            return false;
        }

        return $taxRatePercent;
    }

    /**
     * Strip taxes from value by tax percent
     * @param $valueInclTax
     * @param $taxRatePercent
     * @return float|int
     */
    public function getValueExcludingTax(float $valueInclTax, float $taxRatePercent)
    {
        if(empty($valueInclTax) || empty($taxRatePercent)) {
            return false;
        }
        return number_format(($valueInclTax*100)/(100+$taxRatePercent), 2);
    }

}
