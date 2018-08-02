<?php

namespace snewer\files\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use snewer\files\ModuleTrait;
use yii\web\UploadedFile;

/**
 * From database:
 * @property $id
 * @property $bucket_id
 * @property $path
 * @property $name
 * @property $size
 * @property $integrity
 * @property $uploaded_at
 * @property $uploaded_by
 *
 * @property $url
 * @property \snewer\storage\AbstractBucket $bucket
 * @property string $bucketName
 * @property string $source
 */
class File extends ActiveRecord
{

    use ModuleTrait;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'uploaded_at',
                'updatedAtAttribute' => false
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'uploaded_by',
                'updatedByAttribute' => false
            ]
        ];
    }

    public static function tableName()
    {
        return '{{%files}}';
    }

    private $_bucketName;

    public function getBucketName()
    {
        if ($this->_bucketName === null) {
            $bucketModel = FileBucket::findById($this->bucket_id, true);
            $this->_bucketName = $bucketModel->name;
        }
        return $this->_bucketName;
    }

    /**
     * @return \snewer\storage\AbstractBucket
     */
    public function getBucket()
    {
        return $this->getModule()->getStorage()->getBucket($this->bucketName);
    }

    public function getUrl()
    {
        return $this->getBucket()->getUrl($this->path);
    }

    private $_source = false;

    public function getSource()
    {
        if ($this->_source === false) {
            $this->_source = $this->getBucket()->getSource($this->path);
        }
        return $this->_source;
    }

    public function setSource($source)
    {
        $this->_source = $source;
    }

    public function formattedSize()
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } else {
            $bytes = $bytes . ' B';
        }
        return $bytes;
    }

    public function upload(UploadedFile $file)
    {
        $bucketName = $this->getModule()->bucketName;
        $storageModel = FileBucket::findOrCreateByName($bucketName);
        $storage = $this->getModule()->getStorage()->getBucket($bucketName);
        $source = file_get_contents($file->tempName);
        $this->bucket_id = $storageModel->id;
        $this->name = $file->name;
        $this->size = $file->size;
        $this->path = $storage->upload($source, $file->extension);
        $this->integrity = 'sha384-' . base64_encode(hash('sha384', $source, true));
        return $this->save();
    }

}
