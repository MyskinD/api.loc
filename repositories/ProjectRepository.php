<?php

namespace app\repositories;

use app\models\Projects;
use yii\db\ActiveRecordInterface;

class ProjectRepository implements RepositoryInterface
{
    /**
     * @return array
     */
    public function all(): array
    {
        return Projects::find()
            ->with('contacts')
            ->asArray()
            ->all();
    }

    /**
     * @param int $id
     * @return array
     */
    public function get(int $id): array
    {
        return Projects::find()
            ->with('contacts')
            ->where(['id' => $id])
            ->asArray()
            ->one();
    }

    /**
     * @param array $data
     * @return Projects
     */
    public function add(array $data): Projects
    {
        $project = new Projects();
        $project->name = $data['name'];
        $project->code = $data['code'];
        $project->url = $data['url'];
        $project->budget = $data['budget'];
        $project->save();

        return $project;
    }

    /**
     * @param int $id
     * @param array $insertData
     */
    public function save(int $id, array $insertData): void
    {
        if (!$project = Projects::findOne($id)) {

            throw new NotFoundHttpException('Project was not found');
        }
        if (isset($insertData['name'])) {
            $project->name = $insertData['name'];
        }
        if (isset($insertData['url'])) {
            $project->url = $insertData['url'];
        }
        if (isset($insertData['budget'])) {
            $project->budget = $insertData['budget'];
        }

        $project->save();
    }

    /**
     * @param int $id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(int $id): void
    {
        $project = Projects::findOne($id);
        if (is_null($project)) {

            throw new NotFoundHttpException('Project was not found');
        }

        $project->delete();
    }
}