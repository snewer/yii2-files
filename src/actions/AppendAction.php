<?php

namespace snewer\files\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\web\UploadedFile;
use snewer\files\ModuleTrait;
use snewer\files\models\File;

class AppendAction extends Action
{

    use ModuleTrait;

    public function run()
    {
        $fileId = Yii::$app->request->post('id');
        $source = Yii::$app->request->post('source');
        $source = base64_decode($source);

        $model = File::findOne($fileId);
        if ($model !== null) {
            $success = $model->append($source);
        } else {
            $model = new File();
            $name = Yii::$app->request->post('name');
            $nameParts = explode('.', $name);
            $extension = array_pop($nameParts);
            $name = implode('.', $nameParts);
            $success = $model->uploadBinary($name, $extension, $source);
        }

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