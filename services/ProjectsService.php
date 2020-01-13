<?php

namespace app\services;

use app\models\Contacts;
use app\models\Projects;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Exception;
use Yii;

class ProjectsService
{
    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAll(): array
    {
        return Projects::find()
            ->with('contacts')
            ->asArray()
            ->all();
    }

    /**
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function get(int $id): array
    {
        $project = Projects::find()
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
    public function add(array $data): Projects
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
            $project = new Projects();
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

        return $project;
    }

    /**
     * @param int $id
     * @param array $data
     * @return int
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function save(int $id, array $data): Projects
    {
        if (!$project = Projects::findOne($id)) {

            throw new NotFoundHttpException('Project was not found');
        }
        if ($data['name']) {
            if (!preg_match('/^[a-zA-Z\s]{5,50}$/', $data['name'])) {

                throw new BadRequestHttpException('Enter the correct field NAME');
            }
            $project->name = $data['name'];
        }
        if ($data['url']) {
            if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {

                throw new BadRequestHttpException('Enter the correct field URL');
            }
            $project->url = $data['url'];
        }
        if ($data['budget']) {
            if (!filter_var($data['budget'], FILTER_VALIDATE_INT)) {

                throw new BadRequestHttpException('Enter the correct field BUDGET');
            }
            $project->budget = $data['budget'];
        }
        $project->save();

        return $project;
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function remove(int $id): void
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            Contacts::deleteAll(['project_id' => $id]);

            $project = Projects::findOne($id);
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