<?php

namespace snewer\files\controllers;

use yii\web\Controller;
use snewer\images\ModuleTrait;

/**
 * Class ImageController
 * @package snewer\files\controllers
 * @property \snewer\files\Module $module
 */
class FileController extends Controller
{

    use ModuleTrait;

    public function behaviors()
    {
        return [
            'access' => $this->getModule()->controllerAccess
        ];
    }

    public function actions()
    {
        return [
            'upload' => 'snewer\files\actions\UploadAction',
            'get' => 'snewer\files\actions\GetAction',
            'append' => 'snewer\files\actions\AppendAction',
        ];
    }

}