<?php

namespace Splendid;

/**
 * Form Element Class
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */
class FormElement
{
    public $required = false;

    public $error = null;

    public $name;

    public $label;

    private $type;

    private $result;

    private $classes = array();

    private $afterInput;

    private $items = array();

    private $extensions = array();

    private $checked = array();

    private $selected;

    private $value;

    public function __construct($type, $name, $label = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;

        switch ($this->type)
        {
            case 'file':
                $this->result = "<input type='file' name='" . $this->name . "'";
                break;
            case 'checkbox':
                $this->result .= "<label><input type='checkbox' name='" . $this->name . "[]'";
                break;
            case 'combobox':
            case 'select':
                $this->result .= "<select name='" . $this->name . "'";
                break;
            case 'radio':
                $this->result = "<label><input type='radio' name='" . $this->name . "'";
                break;
            case 'textarea':
                $this->result = "<textarea name='" . $this->name . "'";
                break;
            default:
                $this->result = "<input type='" . $this->type . "' name='" . $this->name . "'";
        }
    }

    public function setItems(array $items = array())
    {
        $this->items = $items;
        return $this;
    }

    public function setExtensions(array $ext = array())
    {
        $this->extensions = $ext;
        return $this;
    }

    public function addItem($item)
    {
        $this->items[] = $item;
        return $this;
    }

    public function addExtension($ext)
    {
        $this->extensions[] = $ext;
        return $this;
    }

    public function setChecked($checked)
    {
        if ($checked == Form::SENT_VALUE) {
            $this->checked = (isset($_POST[$this->name]) ? $_POST[$this->name] : null);
        } else {
            $this->checked = $checked;
        }
        return $this;
    }

    public function setSelected($selected)
    {
        if ($selected == Form::SENT_VALUE) {
            $this->selected = (isset($_POST[$this->name]) ? $_POST[$this->name] : null);
        } else {
            $this->selected = $selected;
        }
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setError()
    {
        $this->classes[] = "error";
        return $this;
    }

    public function hasClass($class = null)
    {
        if ($class) return in_array($class, $this->classes);
        return !empty($this->classes);
    }

    public function addClass($class)
    {
        $this->classes[] = $class;
        return $this;
    }

    public function required()
    {
        $this->result .= ' required="required"';
        $this->required = 1;
        return $this;
    }

    public function addAttr($attr, $val)
    {
        $this->result .= ' ' . $attr . '="' . $val . '"';
        return $this;
    }

    public function addValue($value)
    {
        if ($value == Form::SENT_VALUE) {
            $value = (isset($_POST[$this->name]) ? $_POST[$this->name] : null);
        }

        if ($this->getType() != 'textarea')
            $this->result .= ' value="' . htmlspecialchars($value) . '"';

        $this->value = $value;
        return $this;
    }

    public function combineClasses()
    {
        if (!empty($this->classes)) {
            $this->result .= ' class="';
            foreach($this->classes as $class) {
                $this->result .= ($class == $this->classes[0] ? null : " ") . $class;
            }
            $this->result .= '"';
        }
    }

    public function addAfterInput($value)
    {
        $this->afterInput = $value;
        return $this;
    }

    public function render()
    {
        if ($this->type == 'file')
        {
            if (!empty($this->extensions)) {
                $lastKey = count($this->extensions)-1;
                $this->result .= ' accept="';
                foreach($this->extensions as $key => $ext) {
                    $this->result .= $ext;
                    if ($key != $lastKey) {
                        $this->result .= ", ";
                    }
                }
                $this->result .= '"';
            }
            $this->result .= '>' . PHP_EOL;
            return $this->result;
        }
        else if ($this->type == 'select')
        {
            $this->result .= ">" . PHP_EOL;
            foreach($this->items as $key => $value) {
                $this->result .= "<option value='" . $key . "' ".((!empty($this->selected) and $key == $this->selected) ? "selected='selected'" : null).">" . htmlspecialchars($value) . "</option>" . PHP_EOL;
            }
            $this->result .= "</select>" . PHP_EOL;
            return $this->result;
        }
        else if ($this->type == 'radio')
        {
            $finalResult = "";
            $i = 0;
            foreach($this->items as $value => $label) {
                $finalResult .= $this->result . "value='" . $value . "'";
                if (($this->selected && $value == $this->selected) || (!$this->selected && $i == 0)) {
                    $finalResult .= " checked='checked'";
                }
                $finalResult .= "> " . $label . "</label>" . PHP_EOL;
                $i++;
            }
            return $finalResult;
        }
        else if ($this->type == 'checkbox')
        {
            $finalResult = "";
            foreach($this->items as $key => $value)
            {
                $finalResult .= $this->result . " value='" . $key . "' ".((!empty($this->checked) and in_array($key, $this->checked)) ? "checked" : null).">" . $value . "</label>" . PHP_EOL;
            }
            return $finalResult;
        }
        else if ($this->type == 'textarea')
        {
            $this->result .= '>' . PHP_EOL;
            if (!empty($this->value)) {
                $this->result .= htmlspecialchars($this->value);
            }
            $this->result .= '</textarea>' . PHP_EOL;
            return $this->result;
        }
        else
        {
            if (substr($this->result, -1) !== '>')
                $this->result .= '>';
            if ($this->afterInput)
                $this->result .= $this->afterInput;
            return $this->result;
        }
    }
}