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

    public function getElementHtml()
    {
        $value = $this->getValue() ?: $this->defaultValue;
        $parts = array_filter(preg_split('/[^0-9.]+/', $value), 'strlen');

        $html  = '<input name="' . $this->getName() . '[]"';
        $html .= '       id="' . $this->getHtmlId() . '-y"';
        if ($this->getClass())
            $html .= '   class="' . $this->getClass() . '"';
        if ($this->getOnclick())
            $html .= '   onclick="' . $this->getOnclick() . '"';
        if ($this->getOnchange())
            $html .= '   onchange="' . $this->getOnchange() . '"';
        if ($this->hasTabindex())
            $html .= '   tabindex="' . $this->getTabindex() . '"';
        if ($this->getRequired())
            $html .= '   required';
        if ($this->getDisabled())
            $html .= '   disabled';
        if ($this->getReadonly())
            $html .= '   readonly';
        $html .= '       value="' . $parts[1] . '"';
        $html .= '       type="number"';
        $html .= '       min="0"';
        $html .= '       step="1"';
        $html .= '       style="min-width:12em">';
        $html .= '<br>';
        $html .= '<input name="' . $this->getName() . '[]"';
        $html .= '       id="' . $this->getHtmlId() . '-m"';
        if ($this->getClass())
            $html .= '   class="' . $this->getClass() . '"';
        if ($this->getOnclick())
            $html .= '   onclick="' . $this->getOnclick() . '"';
        if ($this->getOnchange())
            $html .= '   onchange="' . $this->getOnchange() . '"';
        if ($this->hasTabindex())
            $html .= '   tabindex="' . $this->getTabindex() . '"';
        if ($this->getRequired())
            $html .= '   required';
        if ($this->getDisabled())
            $html .= '   disabled';
        if ($this->getReadonly())
            $html .= '   readonly';
        $html .= '       value="' . $parts[2] . '"';
        $html .= '       type="number"';
        $html .= '       min="0"';
        $html .= '       step="1"';
        $html .= '       max="11"';
        $html .= '       style="min-width:12em">';
        $html .= '<br>';
        $html .= '<input name="' . $this->getName() . '[]"';
        $html .= '       id="' . $this->getHtmlId() . '-d"';
        if ($this->getClass())
            $html .= '   class="' . $this->getClass() . '"';
        if ($this->getOnclick())
            $html .= '   onclick="' . $this->getOnclick() . '"';
        if ($this->getOnchange())
            $html .= '   onchange="' . $this->getOnchange() . '"';
        if ($this->hasTabindex())
            $html .= '   tabindex="' . $this->getTabindex() . '"';
        if ($this->getRequired())
            $html .= '   required';
        if ($this->getDisabled())
            $html .= '   disabled';
        if ($this->getReadonly())
            $html .= '   readonly';
        $html .= '       value="' . $parts[3] . '"';
        $html .= '       type="number"';
        $html .= '       min="0"';
        $html .= '       step="1"';
        $html .= '       style="min-width:12em">';

        $html .= $this->getAfterElementHtml();
        return $html;
    }
}
