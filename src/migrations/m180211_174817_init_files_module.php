<?php

namespace snewer\files\migrations;

use yii\db\Migration;

class m180211_174817_init_files_module extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%files}}', [
            'id' => $this->primaryKey()->unsigned(),
            'bucket_id' => $this->integer()->unsigned(),
            'path' => $this->string(),
            'name' => $this->string(),
            'size' => $this->integer(),
            'integrity' => $this->string(),
            'uploaded_at' => $this->integer()->unsigned(),
            'uploaded_by' => $this->integer()->unsigned()
        ]);
        $this->createTable('{{%files_buckets}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()
        ]);
        $this->createIndex('un_files_buckets$name', '{{%files_buckets}}', 'name', true);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%files}}');
        $this->dropTable('{{%files_buckets}}');
    }

}
