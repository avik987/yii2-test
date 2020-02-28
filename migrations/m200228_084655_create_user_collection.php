<?php

class m200228_084655_create_user_collection extends \yii\mongodb\Migration
{
    private $collection = 'user';
    public function up()
    {
        $this->createCollection($this->collection);
        $this->createIndex($this->collection, 'user_id');
        $this->createIndex($this->collection, 'message_id');

    }

    public function down()
    {
        echo "m200228_084655_create_user_collection cannot be reverted.\n";

        return false;
    }
}
