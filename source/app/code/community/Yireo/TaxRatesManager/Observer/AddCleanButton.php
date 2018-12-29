<?php
/**
 * Yireo TaxRatesManager extension for Magento 1
 *
 * @package     Yireo_TaxRatesManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_TaxRatesManager_Observer_AddCleanButton
 */
class Yireo_TaxRatesManager_Observer_AddCleanButton
{
    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Zend_Http_Client_Exception
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Cache_Exception
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Tax_Rate_Toolbar_Add $block */
        $block = $observer->getBlock();
        if (!$block instanceof Mage_Adminhtml_Block_Tax_Rate_Toolbar_Add) {
            return $this;
        }

        $layout = Mage::getSingleton('core/layout');
        $originalButtonBlock = $block->getChild('addButton');

        /** @var Mage_Adminhtml_Block_Widget_Button $buttonBlock */
        $cleanButtonBlock = $layout->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('taxratesmanager')->__('Clean Existing Rates'),
                'onclick' => 'window.location.href=\''.Mage::getUrl('*/taxratesmanager/clean').'\'',
                'class' => 'delete'
            ));

        /** @var Mage_Core_Block_Text_List $newBlock */
        $newBlock = $layout->createBlock('core/text_list');
        $newBlock->append($cleanButtonBlock);
        $newBlock->append($originalButtonBlock);

        $block->setChild('addButton', $newBlock);

        return $this;
    }
}
