<?php

namespace app\models;

use Yii;
use Exception;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

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
     * @throws NotFoundHttpException
     */
    public static function getProjects()
    {
        $projects = self::find()
            ->with('contacts')
            ->asArray()
            ->all()
        ;
        if (is_null($projects)) {

            throw new NotFoundHttpException('Projects was not found');
        }

        return $projects;
    }

    /**
     * @param int $id
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public static function getProject(int $id)
    {
        $project = self::find()
            ->with('contacts')
            ->where(['id' => $id])
            ->asArray()
            ->one()
        ;
        if (is_null($project)) {

            throw new NotFoundHttpException('Project was not found');
        }

        return $project;
    }

    /**
     * @param array $data
     * @return int
     * @throws BadRequestHttpException
     */
    public static function make(array $data): int
    {
        if (!$data['contacts']) {

            throw new BadRequestHttpException('There must be at least one contact');
        }
        if (!$data['name'] || !preg_match('/^[a-zA-Z\s]{5,50}$/', $data['name'])) {

            throw new BadRequestHttpException('Enter the correct field NAME');
        }
        if (!$data['code'] || !preg_match('/^[a-z]{3,10}$/', $data['code'])) {

            throw new BadRequestHttpException('Enter the correct field CODE');
        }
        if (!$data['url'] || !filter_var($data['url'], FILTER_VALIDATE_URL)) {

            throw new BadRequestHttpException('Enter the correct field URL');
        }
        if (!$data['budget'] || !filter_var($data['budget'], FILTER_VALIDATE_INT)) {

            throw new BadRequestHttpException('Enter the correct field BUDGET');
        }

        $contacts = [];
        foreach ($data['contacts'] as $contact) {
            if (!$contact['firstName']) {

                throw new BadRequestHttpException('The field firstName must not be empty');
            }
            if (!$contact['lastName']) {

                throw new BadRequestHttpException('The field lastName must not be empty');
            }
            $pattern = '/^\+(\d){3}\s\((\d){2}\)\s(\d){3}-(\d){2}-(\d){2}$/';
            if (!$contact['phone'] || !preg_match($pattern, $contact['phone'])) {

                throw new BadRequestHttpException('Enter the correct field PHONE');
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
        } catch(Exception $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        return $project->id;
    }

    /**
     * @param int $id
     * @param array $data
     * @return int
     * @throws NotFoundHttpException
     */
    public static function change(int $id, array $data): int
    {
        if (!$project = self::findOne($id)) {

            throw new NotFoundHttpException('Project was not found');
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
     * @param int $id
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public static function remove(int $id)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            Contacts::deleteAll(['project_id' => $id]);

            $project = self::findOne($id);
            if (is_null($project)) {

                throw new NotFoundHttpException('Project was not found');
            }

            $project->delete();
            $transaction->commit();
        } catch(NotFoundHttpException $exception) {
            $transaction->rollBack();

            throw $exception;
        }
    }
}
