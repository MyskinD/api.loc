<?php

namespace app\repositories;

use app\models\Contact;
use Yii;
use yii\web\NotFoundHttpException;

class ContactRepository implements ContactRepositoryInterface
{
    /**
     * @return array
     */
    public function all(): array
    {
        return Contact::find()
            ->asArray()
            ->all();
    }

    /**
     * @param int $id
     * @return array|mixed|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function get(int $id)
    {
        $contact = Contact::find()
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
     * @return Contact
     */
    public function add(array $data): Contact
    {
        if ($data['projectId'] && !is_int($data['projectId'])) {
            throw new BadRequestHttpException('Invalid projectId');
        }

        $contact = new Contact();
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
     * @throws NotFoundHttpException
     */
    public function save(int $id, array $data):void
    {
        if (!$contact = Contact::findOne($id)) {
            throw new NotFoundHttpException('Contact was not found');
        }
        if ($data['projectId'] && !is_int($data['projectId'])) {
            throw new BadRequestHttpException('Invalid projectId');
        }

        $contact->first_name = $data['firstName'];
        $contact->last_name = $data['lastName'];
        $contact->phone = $data['phone'];
        $contact->save();
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(int $id): void
    {
        $contact = Contact::findOne($id);
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
            Contact::tableName(), [
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
        Contact::deleteAll(['project_id' => $id]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getByProjectId(int $id): array
    {
        return Contact::find()
            ->where(['project_id' => $id])
            ->asArray()
            ->all();
    }
}