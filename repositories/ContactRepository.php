<?php

namespace app\repositories;

use app\models\Contacts;
use Yii;

class ContactRepository implements ContactRepositoryInterface
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
        $contact = Contacts::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (is_null($contact)) {

            throw new NotFoundHttpException('Contact was not found');
        }

        return $contact;
    }

    /**
     * @param array $data
     * @return Contacts
     */
    public function add(array $data): Contacts
    {
        if ($data['projectId'] && !is_int($data['projectId'])) {
            throw new BadRequestHttpException('Invalid projectId');
        }

        $contact = new Contacts();
        $contact->project_id = $data['projectId'];
        $contact->first_name = $data['firstName'];
        $contact->last_name = $data['lastName'];
        $contact->phone = $data['phone'];
        $contact->save();

        return $contact;
    }

    /**
     * @param int $id
     * @param array $data
     */
    public function save(int $id, array $data):void
    {
        if (!$contact = Contacts::findOne($id)) {

            throw new NotFoundHttpException('Contact was not found');
        }
        if ($data['projectId'] && !is_int($data['projectId'])) {
            throw new BadRequestHttpException('Invalid projectId');
        }
        if (isset($data['firstName'])) {
            $contact->first_name = $data['firstName'];
        }
        if (isset($data['lastName'])) {
            $contact->last_name = $data['lastName'];
        }
        if (isset($data['phone'])) {
            $contact->phone = $data['phone'];
        }

        $contact->save();
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
     * @param array $data
     * @throws \yii\db\Exception
     */
    public function batchAdd(array $data): void
    {
        Yii::$app->db->createCommand()->batchInsert(
            Contacts::tableName(),
            [
                'project_id',
                'first_name',
                'last_name',
                'phone'
            ],
            $data
        )->execute();
    }

    /**
     * @param int $id
     */
    public function removeByProjectId(int $id): void
    {
        Contacts::deleteAll(['project_id' => $id]);
    }
}