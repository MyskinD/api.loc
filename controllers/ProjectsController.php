<?php

namespace app\controllers;

use app\services\ProjectsService;
use Yii;
use yii\base\Module;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class ProjectsController extends DefaultController
{
    /** @var ProjectsService  */
    protected $projectsService;

    /**
     * ProjectsController constructor.
     * @param $id
     * @param Module $module
     * @param ProjectsService $projectsService
     * @param array $config
     */
    public function __construct
    (
        $id,
        Module $module,
        ProjectsService $projectsService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->projectsService = $projectsService;
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        $projects = $this->projectsService->getProjects();

        return $projects;
    }

    /**
     * @return array
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');

        try {
            $project = $this->projectsService->getProject($id);

            return $project;
        } catch(NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;

            return $this->error($exception->getMessage());
        }
    }

    /**
     * @return array
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->post();

        try {
            $project = $this->projectsService->setProject($data);

            return $project;
        } catch (BadRequestHttpException $exception) {
            Yii::$app->response->statusCode = 400;

            return $this->error($exception->getMessage());
        }
    }

    /**
     * @return array
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $data = Yii::$app->request->post();

        try {
            $project = $this->projectsService->updateProject($id, $data);

            return $project;
        } catch (NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;

            return $this->error($exception->getMessage());
        } catch (BadRequestHttpException $exception) {
            Yii::$app->response->statusCode = 400;

            return $this->error($exception->getMessage());
        }
    }

    /**
     * @return array
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');

        try {
            $this->projectsService->deleteProject($id);
            Yii::$app->response->statusCode = 204;
        } catch (NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;

            return $this->error($exception->getMessage());
        }
    }
}
