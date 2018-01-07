<?php

/**
 * Error Controller
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */
class ErrorController extends BaseController
{
    public function beforeProcess()
    {
        $this->metaHeader['title'] = 'Error 404 - Page not found';
        $this->metaHeader['description'] = 'Sorry, this page doesn\'t exist or was deleted.';
        $this->metaHeader['keywords'] = 'Error 404';
    }

    public function process()
    {
        $this->view = 'errors/404';
    }

}
