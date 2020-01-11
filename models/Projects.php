<?php

namespace app\models;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $url
 * @property int|null $budget
 *
 * @property Contacts[] $contacts
 */
class Projects extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['budget'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 10],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'url' => 'Url',
            'budget' => 'Budget',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contacts::className(), ['project_id' => 'id']);
    }

}
