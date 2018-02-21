<?php

namespace Splendid;

/**
 * Form Framework
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */
class Form
{
    /**
     * For auto value
     */
    const SENT_VALUE = 'sent_value';

    /**
     * URL path for action
     * @var string
     */
    private $action;

    /**
     * Submit method POST/GET
     * @var string
     */
    private $method;

    /**
     * List of input elements
     * @var array
     */
    private $inputs = array();

    /**
     * Allowing file upload
     * @var bool
     */
    protected $fileUpload = false;

    /**
     * List of errors
     * @var array
     */
    public $errors = array();

    /**
     * List of errored elements
     * @var array
     */
    public $errorElements = array();

    /**
     * Element block class (for CSS)
     * @var array
     */
    public $blockClass = array();

    /**
     * Submit element name
     * @var string
     */
    public $submit;

    /**
     * Form layout
     * @var string
     */
    public $layout = "default";

    /**
     * CSRF secured
     * @var string
     */
    private $secured = false;


    /**
     * Constructor
     *
     * @param string
     * @param string
     */
    public function __construct($action = null, $method = 'POST')
    {
        $this->action = $action;
        $this->method = $method;
    }

    /**
     * Form submitted method
     *
     * @return bool
     */
    public function onSubmit()
    {
        if ($this->method == 'GET') {
            if (isset($_GET[$this->submit])) {
                return true;
            }
        } else {
            if (isset($_POST[$this->submit])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if form was submitted successfully
     *
     * @return bool
     */
    public function onSuccess()
    {
        if (!empty($this->errors)) {
            return false;
        }
        foreach ($this->inputs as $name => $input) {
            if (is_object($input)) {
                if ($input->required == 1) {
                    if ($this->method == "POST") {
                        if (!isset($_POST[$name]) or empty($_POST[$name])) {
                            return false;
                        }
                    } else {
                        if (!isset($_GET[$name]) or empty($_GET[$name])) {
                            return false;
                        }
                    }
                }
            }
        }
        if ($this->secured && $this->method == 'POST') {
            if (empty($_POST['token']) || $_SESSION['csrf_token'] != $_POST['token']) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if the form was successfully submitted
     *
     * @return bool
     */
    public function submitted()
    {
        return ($this->onSubmit() && $this->onSuccess());
    }

    /**
     * Add textbox
     *
     * @param string - element name
     * @param string - element label
     * @return FormElement
     */
    public function addText($name, $label = null)
    {
        $this->inputs[$name] = new FormElement('text', $name, $label);
        return $this->inputs[$name];
    }


    /**
     * Add password
     *
     * @param string - element name
     * @param string - element label
     * @return FormElement
     */
    public function addPassword($name, $label = null)
    {
        $this->inputs[$name] = new FormElement('password', $name, $label);
        return $this->inputs[$name];
    }


    /**
     * Add numberbox
     *
     * @param string - element name
     * @param string - element label
     * @return FormElement
     */
    public function addNumber($name, $label = null)
    {
        $this->inputs[$name] = new FormElement('number', $name, $label);
        return $this->inputs[$name];
    }


    /**
     * Add date
     *
     * @param string - element name
     * @param string - element label
     * @return FormElement
     */
    public function addDate($name, $label = NULL)
    {
        $this->inputs[$name] = new FormElement('date', $name, $label);
        return $this->inputs[$name];
    }


    /**
     * Add hidden
     *
     * @param string - element name
     * @return FormElement
     */
    public function addHidden($name)
    {
        $this->inputs[$name] = new FormElement('hidden', $name);
        return $this->inputs[$name];
    }


    /**
     * Add checkbox(es)
     *
     * @param string - element name
     * @param string - element label
     * @param array - of items
     * @return FormElement
     */
    public function addCheckbox($name, $label, $items)
    {
        $this->inputs[$name] = new FormElement('checkbox', $name, $label);
        $this->inputs[$name]->setItems($items);
        return $this->inputs[$name];
    }


    /**
     * Add radiobox
     *
     * @param string - element name
     * @param string - element label
     * @param array - of items
     * @return FormElement
     */
    public function addRadio($name, $label = NULL, array $items = NULL)
    {
        $this->inputs[$name] = new FormElement('radio', $name, $label);
        $this->inputs[$name]->setItems($items);
        return $this->inputs[$name];
    }


    /**
     * Add textarea
     *
     * @param string - element name
     * @param string - element label
     * @param array - of items
     * @return FormElement
     */
    public function addTextArea($name, $label = NULL)
    {
        $this->inputs[$name] = new FormElement('textarea', $name, $label);
        return $this->inputs[$name];
    }


    /**
     * Add File input
     *
     * @param string - element name
     * @param string - element label
     * @param array - of allowed file extensions
     * @return FormElement
     */
    public function addFile($name, $label, array $extensions = NULL)
    {
        $this->inputs[$name] = new FormElement('file', $name, $label);
        if($extensions) $this->inputs[$name]->setExtensions($extensions);
        $this->fileUpload = true;
        return $this->inputs[$name];
    }


    /**
     * Add combobox
     *
     * @param string - element name
     * @param string - element label
     * @param array - of items
     * @return FormElement
     */
    public function addCombobox($name, $label, array $items = NULL)
    {
        $this->inputs[$name] = new FormElement('select', $name, $label);
        $this->inputs[$name]->setItems($items);
        return $this->inputs[$name];
    }


    /**
     * Add submit button
     *
     * @param string - element name
     * @return FormElement
     */
    public function addSubmit($name = "")
    {
        $this->inputs[$name] = new FormElement('submit', $name);
        $this->submit = $name;
        return $this->inputs[$name];
    }


    /**
     * Adds HTML code
     *
     * @param $name
     * @param $html
     * @return FormElement
     */
    public function addHTML($name, $html)
    {
        $this->inputs[$name] = $html;
        return $this->inputs[$name];
    }



    /**
     * Generates form from array
     * @param $data
     */
    public function generateFromArray($data)
    {
        foreach ($data as $input) {
            $el = null;
            $type = (isset($input[0]) ? $input[0] : null);
            $name = (isset($input[1]) ? $input[1] : null);
            $label = (isset($input[2]) ? $input[2] : null);
            $class = (isset($input['class']) ? $input['class'] : null);
            $id = (isset($input['id']) ? $input['id'] : null);
            $value = (isset($input['value']) ? $input['value'] : null);
            $placeholder = (isset($input['placeholder']) ? $input['placeholder'] : null);
            $items = (isset($input['items']) ? $input['items'] : null);
            $required = (isset($input['required']) ? $input['required'] : null);
            $ext = (isset($input['ext']) ? $input['ext'] : null);

            switch ($type) {
                case 'text':
                    $el = $this->addText($name, $label);
                    break;
                case 'password':
                    $el = $this->addPassword($name, $label);
                    break;
                case 'number':
                    $el = $this->addNumber($name, $label);
                    break;
                case 'date':
                    $el = $this->addDate($name, $label);
                    break;
                case 'checkbox':
                    $el = $this->addCheckbox($name, $label, $items);
                    break;
                case 'radio':
                    $el = $this->addRadio($name, $label, $items);
                    break;
                case 'textarea':
                    $el = $this->addTextArea($name, $label);
                    break;
                case 'file':
                    $el = $this->addFile($name, $label, $ext);
                    break;
                case 'combobox':
                case 'select':
                    $el = $this->addCombobox($name, $label, $items);
                    break;
                case 'submit':
                    $el = $this->addSubmit($name);
                    break;
                default:
                    $el = $this->addText($name, $label);
            }

            if (!empty($class)) {
                if (is_array($class)) {
                    foreach ($class as $c) {
                        $el->addClass($c);
                    }
                } else {
                    $el->addClass($class);
                }
            }

            if (!empty($id)) {
                $el->addId($id);
            }

            if (!empty($value)) {
                $el->addValue($value);
            }

            if (!empty($placeholder)) {
                $el->addPlaceHolder($placeholder);
            }

            if (!empty($required)) {
                $el->setRequired();
            }
        }
    }


    /**
     * Add error message
     *
     * @param string $message error message
     * @param string $el element
     */
    public function addError($message, $el = null)
    {
        $this->errors[] = $message;
        if ($el) {
            $this->addErrorElement($el);
        }
    }


    /**
     * Create block class
     *
     * @param string - class name
     */
    public function addBlockClass($class)
    {
        $this->blockClass[] = $class;
    }


    /**
     * Add error element
     *
     * @param string - element name
     */
    public function addErrorElement($element)
    {
        $this->errorElements[] = $element;
    }


    /**
     * Check if form has error
     *
     * @return bool
     */
    public function isError()
    {
        return empty($this->errors) ? (false) : (true);
    }


    /**
     * Gets all errors
     *
     * @return array - of errors
     */
    public function getErrors()
    {
        if (!empty($this->errors)) {
            return $this->errors;
        }
        return null;
    }


    public function getInput($name)
    {
        return $this->inputs[$name];
    }


    /**
     * Sets form HTML layout
     *
     * @param string - layout name
     */
    public function setFormLayout($layout)
    {
        $this->layout = $layout;
    }


    public function startForm()
    {
        echo '<form action="' . $this->action . '" method="' . $this->method . '"'.($this->fileUpload ? ' enctype="multipart/form-data"' : null).'>' . PHP_EOL;
    }

    public function endForm()
    {
        echo '</form>' . PHP_EOL;
    }

    /**
     * Render form
     * @param string - render element
     */
    public function render($element = null)
    {
        if ($element) {
            $this->inputs[$element]->render();
            return null;
        }

        $this->startForm();
        foreach($this->inputs as $input) {
            if (is_string($input)) {
                echo $input;
            } else {
                if (!empty($this->errorElements)) {
                    if (in_array($input->name, $this->errorElements)) {
                        $input->setError();
                    }
                }
                if (is_object($input)) $input->combineClasses();
                if ($input->getType() == "hidden") {
                    echo $input->render() . PHP_EOL;
                } else {
                    require("../app/views/form_layouts/" . $this->layout . ".php");
                }
            }
        }
        if ($this->secured && $this->method == 'POST') {
            echo "<div style='display: none;'><input type='hidden' name='token' value='".$_SESSION['csrf_token']."' /></div>";
        }
        $this->endForm();
    }

    public function secureCSRF()
    {
        $this->secured = true;
    }
}