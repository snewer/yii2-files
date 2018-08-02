<?php

namespace snewer\files;

use Yii;

/**
 * Class ModuleTrait
 * @package snewer\files
 * @property Module $module
 */
trait ModuleTrait
{

    /**
     * @return Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('files', true);
    }

}