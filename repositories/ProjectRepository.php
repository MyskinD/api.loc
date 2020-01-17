<?php

namespace app\repositories;

use app\models\Project;
use yii\web\NotFoundHttpException;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * @return array
     */
    public function all(): array
    {
        return Project::find()
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
        $project = Project::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (is_null($project)) {

            throw new NotFoundHttpException('Project was not found');
        }

        return $project;
    }

    /**
     * @param array $data
     * @return Project
     */
    public function add(array $data): Project
    {
        $project = new Project();
        $project->name = $data['name'];
        $project->code = $data['code'];
        $project->url = $data['url'];
        $project->budget = $data['budget'];
        $project->save();

        return $project;
    }

    /**
     * @param int $id
     * @param array $data
     * @throws NotFoundHttpException
     */
    public function save(int $id, array $data): void
    {
        if (!$project = Project::findOne($id)) {

            throw new NotFoundHttpException('Project was not found');
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

        $project->save();
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(int $id): void
    {
        $project = Project::findOne($id);

        if (is_null($project)) {

            throw new NotFoundHttpException('Project was not found');
        }

        $project->delete();
    }
}