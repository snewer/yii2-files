<?php

namespace snewer\files\widgets;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use snewer\files\assets\WidgetAsset;

class FileUploadWidget extends InputWidget
{

    /**
     * Загрузка файлов по-частям.
     * @var bool
     */
    public $chunkUpload = false;

    /**
     * Размер загружаемых частей файла.
     * @var int
     */
    public $chunkSize = 1024 * 1024 * 2;

    /**
     * Идентификатор используемого модуля.
     * Используется для генерации ссылок по-умолчанию
     * в методе init().
     * @var string
     */
    public $moduleId = 'files';

    /**
     * Ссылка на действие получения файла.
     * @var string|null
     */
    public $getFileUrl;

    /**
     * Ссылка на действие получения файла.
     * @var string|null
     */
    public $uploadFileUrl;

    /**
     * Ссылка на действие получения файла.
     * @var string|null
     */
    public $appendFileUrl;

    /**
     * Jquery asset bundle
     * @var string
     */
    public $jqueryAsset = 'yii\web\JqueryAsset';

    /**
     * @return string
     */
    private function getInput()
    {
        if (isset($this->options['name'])) {
            $name = $this->options['name'];
        } else {
            $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
        }
        if (isset($this->options['value'])) {
            $value = $this->options['value'];
        } else {
            $value = $this->hasModel() ? Html::getAttributeValue($this->model, $this->attribute) : $this->value;
        }
        return Html::hiddenInput($name, $value, $this->options);
    }

    protected function registerAssets()
    {
        if ($this->jqueryAsset) {
            $this->view->registerAssetBundle($this->jqueryAsset);
        }
        $widgetAsset = $this->view->registerAssetBundle(WidgetAsset::className());
    }

    public function init()
    {
        parent::init();
        $this->registerAssets();
        if ($this->getFileUrl === null) {
            $this->getFileUrl = Url::to(['/' . $this->moduleId . '/file/get']);
        }
        if ($this->uploadFileUrl === null) {
            $this->uploadFileUrl = Url::to(['/' . $this->moduleId . '/file/upload']);
        }
        if ($this->appendFileUrl === null) {
            $this->appendFileUrl = Url::to(['/' . $this->moduleId . '/file/append']);
        }
    }

    public function run()
    {
        $options = [
            'urls' => [
                'getFile' => $this->getFileUrl,
                'uploadFile' => $this->uploadFileUrl,
                'appendFile' => $this->appendFileUrl
            ],
            'chunkUpload' => $this->chunkUpload,
            'chunkSize' => $this->chunkSize
        ];
        $js = PHP_EOL . 'jQuery("#' . $this->options['id'] . '").FileUploadWidget(' . Json::encode($options) . ');';
        $this->view->registerJs($js);
        return $this->getInput();
    }

}