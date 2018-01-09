<?php

/**
 * Application - Router
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */
class Application extends BaseController
{
    /**
     * @var stdClass
     */
    protected $controller;

    /**
     * @var bool
     */
    protected $isAjax = false;

    /**
     * Application constructor
     */
    public function __construct()
    {
        $this->secureSession();
    }

    /**
     * Setting Controller from URL
     *
     * @param string - Controller name
     */
    public function setController($class)
    {
        $newClass = ucFirst($class) . 'Controller';
        if ($newClass == 'HomeController') {
            $newClass = 'IndexController';
        }

        if (file_exists( dirname(__DIR__) . '/app/controllers/' . $newClass . ".php")) {
            $this->controller = new $newClass;
            if ($newClass == 'AjaxController') {
                $this->isAjax = true;
            }
        } else {
            $this->controller = new ErrorController;
        }
    }


    /**
     * Processing controller
     *
     * @param array - of parameters in URL
     */
    public function process()
    {
        if (empty($this->controller)) {
            $this->controller = new IndexController;
        }

        $this->controller->params = $this->params;
        $this->controller->beforeProcess();
        $this->controller->process();
        $this->controller->afterProcess();
        $this->data['controller'] = (!empty($this->params[0])) ? $this->params[0] : 'index';
        if (!$this->isAjax) $this->setLayout();

        if (USE_LANG && isset($_SESSION['lang'])) $this->lang = $_SESSION['lang'];
    }


    /**
     * Setting layout
     */
    private function setLayout()
    {
        $this->data["header"] = $this->controller->metaHeader;
        $this->view = $this->controller->layout;
    }

}