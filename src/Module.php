<?php

namespace snewer\files;

use Yii;
use yii\filters\AccessControl;
use yii\base\InvalidConfigException;

/**
 * Class Module
 * @package snewer\files
 * @property \snewer\storage\StorageManager $storage
 */
class Module extends \yii\base\Module
{

    /**
     * @var string|\snewer\storage\StorageManager
     */
    private $_storage = 'storage';

    public $bucketName;

    public $controllerAccess;

    public function init()
    {
        parent::init();
        if ($this->bucketName === null) {
            throw new InvalidConfigException('Необходимо установить название хранилища для загрузки файлов \'Module::$bucketName\'.');
        }
        if ($this->controllerAccess === null) {
            $this->controllerAccess = [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ];
        }
    }

    public function setStorage($value)
    {
        if (is_string($value)) {
            $this->_storage = $value;
        } else {
            $this->_storage = Yii::createObject($value);
        }
    }

    /**
     * @return \snewer\storage\StorageManager
     */
    public function getStorage()
    {
        if (is_string($this->_storage)) {
            return Yii::$app->get($this->_storage, true);
        } else {
            return $this->_storage;
        }
    }

}