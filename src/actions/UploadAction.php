<?php

namespace snewer\files\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\web\UploadedFile;
use snewer\files\ModuleTrait;
use snewer\files\models\File;

class UploadAction extends Action
{

    use ModuleTrait;

    public function run()
    {
        $file = UploadedFile::getInstanceByName('file');
        $model = new File();
        $success = $model->uploadByUploadedFile($file);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => $success,
            'file' => [
                'id' => $model->id,
                'name' => $model->name,
                'url' => $model->url,
                'size' => $model->formattedSize()
            ]
        ];


    }

}