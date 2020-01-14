<?php

namespace app\repositories;

use app\models\Projects;

class ProjectRepository implements ProjectRepositoryInterface
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
        $project = Projects::find()
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
     * @param array $data
     */
    public function save(int $id, array $data): void
    {
        if (!$project = Projects::findOne($id)) {

            throw new NotFoundHttpException('Project was not found');
        }
        if (isset($data['name'])) {
            $project->name = $data['name'];
        }
        if (isset($data['url'])) {
            $project->url = $data['url'];
        }
        if (isset($data['budget'])) {
            $project->budget = $data['budget'];
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