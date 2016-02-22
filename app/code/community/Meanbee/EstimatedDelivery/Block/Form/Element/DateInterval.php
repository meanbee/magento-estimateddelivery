<?php
class Meanbee_EstimatedDelivery_Block_Form_Element_DateInterval
    extends Varien_Data_Form_Element_Abstract
{
    private $defaultValue;

    public function __construct($config)
    {
        $this->defaultValue = $config['value'];
        parent::__construct($config);
    }

    /**
     * Creates a 3 numeric input field corresponding to number of years, months and
     * days in the interval respectively.
     *
     * @return string HTML of field.
     */
    public function getElementHtml()
    {
        $value = $this->getValue() ?: $this->defaultValue;
        $parts = array_filter(preg_split('/[^0-9.]+/', $value), 'strlen');

        $yearFieldAttr = array(
            'id'          => $this->getHtmlId() . '-y',
            'min'         => 0,
            'name'        => $this->getName() . '[]',
            'placeholder' => Mage::helper('meanbee_estimateddelivery')->__('Years'),
            'step'        => 1,
            'style'       => 'min-width:12em',
            'type'        => 'number',
            'value'       => $parts[1]
        );
        $monthFieldAttr = array(
            'id'          => $this->getHtmlId() . '-m',
            'max'         => 11,
            'min'         => 0,
            'name'        => $this->getName() . '[]',
            'placeholder' => Mage::helper('meanbee_estimateddelivery')->__('Months'),
            'step'        => 1,
            'style'       => 'min-width:12em',
            'type'        => 'number',
            'value'       => $parts[2]
        );
        $yearFieldAttr = array(
            'id'          => $this->getHtmlId() . '-d',
            'min'         => 0,
            'name'        => $this->getName() . '[]',
            'placeholder' => Mage::helper('meanbee_estimateddelivery')->__('Days'),
            'step'        => 1,
            'style'       => 'min-width:12em',
            'type'        => 'number',
            'value'       => $parts[3]
        );

        foreach (array('class', 'onchange', 'onclick') as $attribute) {
            if ($this->getData($attribute)) {
                $yearFieldAttr[$attribute] = $this->getData($attribute);
                $monthFieldAttr[$attribute] = $this->getData($attribute);
                $dayFieldAttr[$attribute] = $this->getData($attribute);
            }
        }
        foreach (array('tabindex') as $attribute) {
            if ($this->hasData($attribute)) {
                $yearFieldAttr[$attribute] = $this->getData($attribute);
                $monthFieldAttr[$attribute] = $this->getData($attribute);
                $dayFieldAttr[$attribute] = $this->getData($attribute);
            }
        }
        foreach (array('disabled', 'readonly') as $attribute) {
            if ($this->getData($attribute)) {
                $yearFieldAttr[$attribute] = null;
                $monthFieldAttr[$attribute] = null;
                $dayFieldAttr[$attribute] = null;
            }
        }

        $html  = $this->createElement('input', $yearFieldAttr);
        $html .= '<br>';
        $html .= $this->createElement('input', $monthFieldAttr);
        $html .= '<br>';
        $html .= $this->createElement('input', $dayFieldAttr);

        return $html;
    }

    /**
     * Creates a unclosed HTML tag from tag name and associative array of attributes.
     *
     * @param  string $tagName    Tag name of the HTML element.
     * @param  array  $attributes Mapping of attributes to values. Use the value
     *                            `null` for a boolean attribute.
     * @return string             HTML representing constructed element.
     */
    private function createElement($tagName, $attributes)
    {
        $html = '<' . $tagName;
        foreach ($attributes as $attribute => $value) {
            if ($value === null) {
                $html .= ' ' . $attribute;
            } else {
                $html .= ' ' . $attribute . '="' . $value . '"';
            }
        }
        $html .= '>';
        return $html;
    }
}
