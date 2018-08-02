<?php

namespace snewer\files\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;
use snewer\files\models\File;

class GetAction extends Action
{

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if ($id) {
            $file = File::findOne($id);
            if ($file) {
                return [
                    'success' => true,
                    'file' => [
                        'id' => $file->id,
                        'name' => $file->name,
                        'url' => $file->url,
                        'size' => $file->formattedSize()
                    ]
                ];
            }
        }
        return [
            'success' => false
        ];
    }

}