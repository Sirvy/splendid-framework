<?php

/**
 * Base Controller
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */
class BaseController extends Controller
{
    /**
     * Startup method
     */
    protected function beforeProcess()
    {
        parent::beforeProcess();

        $this->secureDos();

        $this->service = new \Splendid\Service();
        $this->service->store('db', new \Splendid\Db());
        $this->service->store('user', new \Splendid\User());
        $this->db = $this->service->get('db');
        $this->user = $this->service->get('user');

        $this->metaHeader = array(
            'title'         =>  'Splendid Framework',
            'author'        =>  'Bobby Tran',
            'description'   =>  '',
            'keywords'      =>  '',
        );
    }

    /**
     * Creates new block for includes in views
     * @param $key
     * @param $view
     */
    protected function addBlock($key, $view)
    {
        $this->block[$key] = $view;
    }

    /**
     * Includes block file
     * @param $key
     */
    protected function includeBlock($key)
    {
        if ($this->block[$key]) {
            if (!file_exists(dirname(__DIR__) . '/views/' . $this->block[$key] . '.php')) {
                if (DEV_MODE) echo 'Block <b>' . $this->block[$key] . '</b> not found.';
                else header('HTTP/1.1 500 Internal Server Error');
            } else {
                extract($this->secureXSS($this->data));
                extract($this->data, EXTR_PREFIX_ALL, '');
                require(dirname(__DIR__) . '/views/' . $this->block[$key] . '.php');
            }
        }
    }

    /**
     * Render $view with extracted variables
     * Throws Internal Error 500 if view not found
     */
    public function render()
    {
        if ($this->view) {
            if (!file_exists(dirname(__DIR__) . '/views/' . $this->view . '.php')) {
                if (DEV_MODE) echo 'View <b>' . $this->view . '</b> not found.';
                else header('HTTP/1.1 500 Internal Server Error');
            } else {
                extract($this->secureXSS($this->data));
                extract($this->data, EXTR_PREFIX_ALL, '');

                if (USE_LANG) {
                    $langT = require(LANG_DIR . '/' . $this->lang . '.php');
                    if (USE_LANG) $_tr = json_decode(json_encode($langT));
                }
                require(dirname(__DIR__) . '/views/' . $this->view . '.php');
            }
        }
    }
}