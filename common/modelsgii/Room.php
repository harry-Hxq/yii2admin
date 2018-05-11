<?php

namespace common\modelsgii;

use Yii;

/**
 * This is the model class for table "{{%ad}}".
 *
 * @property integer $id
 * @property string $image
 * @property integer $type
 * @property string $title
 * @property string $url
 * @property integer $sort
 * @property integer $status
 */
class Room extends \common\core\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%room}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }
}
