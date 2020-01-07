<?php

namespace app\models;

use Yii;

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

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getProjects()
    {
        return self::find()
            ->with('contacts')
            ->asArray()
            ->all()
        ;
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getProject($id)
    {
        return self::find()
            ->with('contacts')
            ->where(['id' => $id])
            ->asArray()
            ->one()
        ;
    }

    /**
     * @param $data
     * @return int
     * @throws \Exception
     */
    public static function make($data)
    {
        if (!$data['contacts']) {

            throw new \Exception('There must be at least one contact');
        }
        if (!$data['name'] || !preg_match('/^[a-zA-Z\s]{5,50}$/', $data['name'])) {

            throw new \Exception('Enter the correct field NAME');
        }
        if (!$data['code'] || !preg_match('/^[a-z]{3,10}$/', $data['code'])) {

            throw new \Exception('Enter the correct field CODE');
        }
        if (!$data['url'] || !filter_var($data['url'], FILTER_VALIDATE_URL)) {

            throw new \Exception('Enter the correct field URL');
        }
        if (!$data['budget'] || !filter_var($data['budget'], FILTER_VALIDATE_INT)) {

            throw new \Exception('Enter the correct field BUDGET');
        }

        $contacts = [];
        foreach ($data['contacts'] as $contact) {

            if (!$contact['firstName']) {

                throw new \Exception('The field firstName must not be empty');
            }
            if (!$contact['lastName']) {

                throw new \Exception('The field lastName must not be empty');
            }

            $pattern = '/^\+(\d){3}\s\((\d){2}\)\s(\d){3}-(\d){2}-(\d){2}$/';
            if (!$contact['phone'] || !preg_match($pattern, $contact['phone'])) {

                throw new \Exception('Enter the correct field PHONE');
            }

            $contacts[] = [
                'firstName' => $contact['firstName'],
                'lastName' => $contact['lastName'],
                'phone' => $contact['phone'],
            ];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $project = new self();
            $project->name = $data['name'];
            $project->code = $data['code'];
            $project->url = $data['url'];
            $project->budget = $data['budget'];
            $project->save();

            $insertData = [];
            foreach ($contacts as $value) {
                $insertData[] = [
                    $project->id,
                    $value['firstName'],
                    $value['lastName'],
                    $value['phone'],
                ];
            }

            Yii::$app->db->createCommand()->batchInsert(
                Contacts::tableName(),
                [
                    'project_id',
                    'firstName',
                    'lastName',
                    'phone'
                ],
                $insertData
            )->execute();

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();

            throw $e;
        }

        return $project->id;
    }

    /**
     * @param $id
     * @param $data
     * @return int
     * @throws \Exception
     */
    public static function change($id, $data)
    {
        if (!$project = self::findOne($id)) {

            throw new \Exception('Project could not be changed');
        }
        if ($data['name'] && preg_match('/^[a-zA-Z\s]{5,50}$/', $data['name'])) {
            $project->name = $data['name'];
        }
        if ($data['url'] && filter_var($data['url'], FILTER_VALIDATE_URL)) {
            $project->url = $data['url'];
        }
        if ($data['budget'] && filter_var($data['budget'], FILTER_VALIDATE_INT)) {
            $project->budget = $data['budget'];
        }
        $project->save();

        return $project->id;
    }

    /**
     * @param $id
     * @throws \Throwable
     */
    public static function remove($id)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!Contacts::deleteAll(['project_id' => $id])) {

                throw new \Exception('Contacts could not be deleted');
            }

            $project = self::findOne($id);
            if (!$project->delete()) {

                throw new \Exception('Project could not be deleted');
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();

            throw $e;
        }
    }
}
