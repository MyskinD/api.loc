<?php

namespace app\controllers;

use yii\base\Controller;
use yii\base\Module;

class DefaultController extends Controller
{
    /**
     * DefaultController constructor.
     * @param $id
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * @param string $message
     * @return array
     */
    protected  function error(string $message): array
    {
        return [
            'error' => $message
        ];
    }

}