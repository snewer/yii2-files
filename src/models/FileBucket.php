<?php

namespace snewer\files\models;

use yii\base\ErrorException;
use yii\db\ActiveRecord;

/**
 * Class FileBucket
 * @package snewer\files\models
 * @property $id
 * @property $name
 */
class FileBucket extends ActiveRecord
{

    /**
     * @var FileBucket[]
     */
    private static $_buckets;

    private static $_bucketsNamesToIdMap;

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName()
    {
        return '{{%files_buckets}}';
    }

    private static function loadBuckets()
    {
        if (self::$_buckets === null) {
            self::$_buckets = self::find()->indexBy('id')->all();
            if (self::$_buckets) {
                self::$_bucketsNamesToIdMap = [];
                foreach (self::$_buckets as $id => $bucket) {
                    self::$_bucketsNamesToIdMap[$bucket->name] = (int)$id;
                }
            } else {
                self::$_buckets = [];
                self::$_bucketsNamesToIdMap = [];
            }
        }
    }

    /**
     * @param integer $id
     * @param boolean $throwException
     * @throws ErrorException
     * @return array|null|self
     */
    public static function findById($id, $throwException = false)
    {
        self::loadBuckets();
        $id = (int)$id;
        if (!isset(self::$_buckets[$id])) {
            if ($throwException) {
                throw new ErrorException("Хранилище с идентификатором '{$id}' не найдено в базе данных.");
            } else {
                return false;
            }
        }
        return self::$_buckets[$id];
    }

    /**
     * @param string $name
     * @param boolean $throwException
     * @throws ErrorException
     * @return array|null|self
     */
    public static function findByName($name, $throwException = false)
    {
        self::loadBuckets();
        if (!isset(self::$_bucketsNamesToIdMap[$name])) {
            if ($throwException) {
                throw new ErrorException("Хранилище с названием '{$name}' не найдено в базе данных.");
            } else {
                return false;
            }
        }
        return self::$_buckets[self::$_bucketsNamesToIdMap[$name]];
    }

    /**
     * @param $name
     * @return array|null|self
     */
    public static function findOrCreateByName($name)
    {
        $model = self::findByName($name, false);
        if (!$model) {
            $model = new self;
            $model->name = $name;
            $model->save(false);
            self::$_buckets[(int)$model->id] = $model;
            self::$_bucketsNamesToIdMap[$name] = (int)$model->id;
        }
        return $model;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::class, ['storage_id' => 'id']);
    }

}