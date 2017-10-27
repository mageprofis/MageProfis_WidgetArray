<?php

class MageProfis_WidgetArray_Block_Adminhtml_Widget_Array_Rows extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_template = 'mp_widgetarray/array.phtml';

    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Add a column to array-grid
     *
     * @param string $name
     * @param array $params
     */
    public function addColumn($name, $params)
    {
        $this->_columns[$name] = array(
            'label'     => empty($params['label']) ? 'Column' : $params['label'],
            'size'      => empty($params['size'])  ? false    : $params['size'],
            'style'     => empty($params['style'])  ? null    : $params['style'],
            'class'     => empty($params['class'])  ? null    : $params['class'],
            'source_model' => empty($params['source_model'])  ? null    : $params['source_model'],
            'type'      => empty($params['type'])  ? null    : $params['type'],            
            'renderer'  => false,
        );
        if ((!empty($params['renderer'])) && ($params['renderer'] instanceof Mage_Core_Block_Abstract)) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
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
                    'type' => isset($col['type']) ? $col['type'] : null,
                    'source_model' => isset($col['source_model']) ? $col['source_model'] : null,
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

        if($column['type']){
            $className = 'Varien_Data_Form_Element_'.ucfirst(strtolower($column['type']));
            
            if(class_exists($className)){
                $element = new $className($column);                
                $element->setForm($this->getElement()->getForm());
                $element->setName($inputName);
                $element->setId("input_#{_id}_" . $columnName);

                if($column['source_model']){
                    $sourceModel = Mage::getModel($column['source_model']);
                    $element->setValues($sourceModel->toOptionArray());
                }

                return trim(preg_replace( "/\r|\n/", "", $element->getElementHtml() ));                
            }

            try {
                $block = Mage::app()->getLayout()->createBlock($column['type']);
                $element = new Varien_Data_Form_Element_Text();
                $element->setForm($this->getElement()->getForm());
                $element->setName($inputName);
                $element->setId("input_#{_id}_" . $columnName);
                return trim(preg_replace( "/\r|\n/", "", "<table>" . $block->render($element) . "</table>"));
            }
            catch(Mage_Core_Exception $e) {
                //supress the error in the event a block doesn't exist.
            }
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
