<?php

/**
 * Ajax Controller
 *
 * @author		Bobby Tran <bobby-tran@email.cz>
 * @copyright	Copyright (c) 2017, Bobby Tran
 */
class AjaxController extends BaseController
{
    protected function beforeProcess() {}

    public function process()
    {
        $this->view = 'index/index';
    }

    protected function afterProcess()
    {
        $this->render();
    }

}
