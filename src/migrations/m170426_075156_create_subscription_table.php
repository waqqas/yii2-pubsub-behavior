<?php

use yii\db\Migration;

/**
 * Handles the creation of table `subscription`.
 */
class m170426_075156_create_subscription_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'models' => $this->string(1024)->notNull()->defaultValue('[]'),
            'events' => $this->string(1024)->notNull()->defaultValue('[]'),
            'url' => $this->string(1024)->notNull()->defaultValue(''),
            'requestMethod' => $this->string(16)->notNull()->defaultValue('post'),
            'enabled' => $this->boolean()->notNull()->defaultValue(true),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%subscription}}');
    }
}
