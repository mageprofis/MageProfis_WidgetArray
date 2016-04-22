<?php

class MageProfis_WidgetArray_Block_Adminhtml_Widget_Array_Rows extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_template = 'mp_widgetarray/array.phtml';

    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $config = $this->getConfig();

        if (isset($config['columns']) && is_array($config['columns']) && $config['columns']) {
            foreach ($config['columns'] as $colKey => $col) {
                $this->addColumn($colKey, array(
                    'label' => isset($col['label']) ? $col['label'] : $colKey,
                ));
            }
        } else {
            $this->addColumn('text', array(
                'label' => Mage::helper('mp_widgetarray')->__('Text'),
            ));
        }

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('mp_widgetarray')->__('Add Link');
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = 'temporary[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
                ->toHtml();
        }

        return '<input type="text" id="input_#{_id}_' . $columnName . '" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="widget-array-input ' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    }

    /**
     * Obtain existing data from form element
     *
     * Each row will be instance of Varien_Object
     *
     * @return array
     */
    public function getArrayRows()
    {
        if (null !== $this->_arrayRowsCache) {
            return $this->_arrayRowsCache;
        }
        $result = array();
        /** @var Varien_Data_Form_Element_Abstract */
        $element = $this->getElement();
        if ($element->getValue()) {
            $value = json_decode(base64_decode($element->getValue()), true);
            if (is_array($value)) {
                foreach ($value as $rowId => $row) {
                    foreach ($row as $key => $value) {
                        $row[$key] = $this->escapeHtml($value);
                    }
                    $row['_id'] = $rowId;
                    $result[$rowId] = new Varien_Object($row);
                    $this->_prepareArrayRow($result[$rowId]);
                }
            }
        }
        $this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;
    }
}
