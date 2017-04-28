<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%subscription}}".
 *
 * @property integer $id
 * @property array $models
 * @property array $events
 * @property string $url
 * @property string $requestMethod
 * @property integer $enabled
 */
class Subscription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subscription}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled'], 'integer'],
            [['models', 'events', 'url'], 'string', 'max' => 1024],
            [['requestMethod'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'models' => 'Models',
            'events' => 'Events',
            'url' => 'Url',
            'requestMethod' => 'Request Method',
            'enabled' => 'Enabled',
        ];
    }

    /**
     * @param string $name
     * @return array|mixed
     */
    public function __get($name)
    {
        $value = parent::__get($name);
        switch ($name) {
            case 'events':
            case 'models':
                $value = json_decode($value, true);
                break;
            default:
        }
        return $value;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'events':
            case 'models':
                $value = json_encode($value);
                break;
        }

        parent::__set($name, $value);
    }

}
