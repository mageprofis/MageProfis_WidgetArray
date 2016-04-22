<?php

class MageProfis_WidgetArray_Block_Adminhtml_Widget_Array extends Mage_Adminhtml_Block_Widget
{
    /**
     * Prepare element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $elements = $this->getLayout()->createBlock('mp_widgetarray/adminhtml_widget_array_rows')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setHtmlId($uniqId)
        ;

        $element->setData('after_element_html', $elements->toHtml());

        return $element;
    }
}
