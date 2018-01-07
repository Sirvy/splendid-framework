<?php

/**
 * Index Controller
 *
 * @author		Bobby Tran <bobby-tran@email.cz>
 * @copyright	Copyright (c) 2017, Bobby Tran
 */
class IndexController extends BaseController
{
    public function beforeProcess()
    {
        parent::beforeProcess();
    }

    public function process()
    {
        $this->view = 'index/index';
    }
}
