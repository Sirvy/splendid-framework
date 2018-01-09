<?php

/**
 * Ajax Controller
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */
class AjaxController extends BaseController
{
    protected function beforeProcess()
    {
        if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['csrf_token']) {
            $this->redirect('error');
        }
    }

    public function process()
    {
        $this->view = 'index/index';
    }

    protected function afterProcess()
    {
        $this->render();
    }

}
