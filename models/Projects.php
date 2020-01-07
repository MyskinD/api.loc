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
     * @param $project
     * @param $contacts
     * @return int
     * @throws \Exception
     */
    public static function _create($project, $contacts)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $newProject = new self();
            $newProject->name = $project['name'];
            $newProject->code = $project['code'];
            $newProject->url = $project['url'];
            $newProject->budget = $project['budget'];

            if (!$newProject->save()) {
                $transaction->rollBack();

                throw new \Exception('Failed to add project');
            }

            $insertData = [];
            foreach ($contacts as $value) {
                $insertData[] = [
                    $newProject->id,
                    $value['firstName'],
                    $value['lastName'],
                    $value['phone'],
                ];
            }

            $insertedCount = Yii::$app->db->createCommand()->batchInsert(
                Contacts::tableName(),
                ['project_id', 'firstName', 'lastName', 'phone'],
                $insertData
            )->execute();

            if (count($insertData) != $insertedCount) {
                $transaction->rollBack();

                throw new \Exception('Contacts add failed to complete');
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();

            throw new \Exception($e->getMessage());
        }

        return $newProject->id;
    }

    /**
     * @param $id
     * @param $data
     * @return int
     * @throws \Exception
     */
    public static function _update($id, $data)
    {
        if (!$project = self::findOne($id)) {
            throw new \Exception('Project could not be changed');
        }
        if ($data['name']) {
            $project->name = $data['name'];
        }
        if ($data['url']) {
            $project->url = $data['url'];
        }
        if ($data['budget']) {
            $project->budget = $data['budget'];
        }
        if (!$project->save()) {
            throw new \Exception('Project could not be changed');
        }

        return $project->id;
    }

    /**
     * @param $id
     * @throws \Throwable
     */
    public static function _delete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!Contacts::deleteAll(['project_id' => $id])) {
                $transaction->rollBack();

                throw new \Exception('Project could not be deleted');
            }

            $project = self::findOne($id);
            if (!$project->delete()) {
                $transaction->rollBack();

                throw new \Exception('Project could not be deleted');
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();

            throw new \Exception($e->getMessage());
        }
    }
}
