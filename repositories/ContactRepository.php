<?php

namespace app\repositories;

use app\models\Contacts;
use Yii;

class ContactRepository implements RepositoryInterface
{
    /**
     * @return array
     */
    public function all(): array
    {
        return Contacts::find()
            ->asArray()
            ->all();
    }

    /**
     * @param int $id
     * @return array|mixed|null|\yii\db\ActiveRecord
     */
    public function get(int $id)
    {
        return Contacts::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();
    }

    /**
     * @param array $insertData
     * @return mixed|void
     * @throws \yii\db\Exception
     */
    public function add(array $insertData): void
    {
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
    }

    public function save(int $id, array $data)
    {
        // TODO: Implement save() method.
    }

    /**
     * @param int $id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(int $id): void
    {
        $contact = Contacts::findOne($id);
        if (is_null($contact)) {

            throw new NotFoundHttpException('Contact was not found');
        }

        $contact->delete();
    }

    /**
     * @param int $projectId
     */
    public function removeByProjectId(int $projectId): void
    {
        Contacts::deleteAll(['project_id' => $projectId]);
    }
}