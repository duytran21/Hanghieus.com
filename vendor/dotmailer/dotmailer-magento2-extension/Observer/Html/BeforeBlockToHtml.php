<?php

namespace Dotdigitalgroup\Email\Observer\Html;

/**
 * Sales rule coupon new columns (expiration_data, generated_by_dotmailer).
 */
class BeforeBlockToHtml implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return null
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $grid = $observer->getBlock();

        /**
         * \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Grid
         */
        if ($grid instanceof \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Grid) {
            $grid->addColumnAfter(
                'expiration_date',
                [
                    'header' => __('Expiration date'),
                    'index' => 'expiration_date',
                    'type' => 'datetime',
                    'default' => '',
                    'align' => 'center',
                    'width' => '160'
                ],
                'created_at'
            )->addColumnAfter(
                'generated_by_dotmailer',
                [
                    'header' => __('Generated By dotmailer'),
                    'index' => 'generated_by_dotmailer',
                    'type' => 'options',
                    'default' => '',
                    'options' => ['null' => 'No', '1' => 'Yes'],
                    'width' => '30',
                    'align' => 'center',
                    'filter_condition_callback' => [$this, 'filterCallbackContact']
                ],
                'expiration_date'
            );
        }
    }

    /**
     * Callback action for .
     *
     * @param mixed $collection
     * @param mixed $column
     *
     * @return null
     */
    protected function filterCallbackContact($collection, $column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex()
            : $column->getIndex();
        $value = $column->getFilter()->getValue();
        if ($value == 'null') {
            $collection->addFieldToFilter($field, ['null' => true]);
        } else {
            $collection->addFieldToFilter($field, ['notnull' => true]);
        }
    }
}