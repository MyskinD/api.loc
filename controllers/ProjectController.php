<?php

namespace app\controllers;

use app\services\ContactService;
use app\services\ProjectService;
use Yii;
use yii\base\Module;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class ProjectController extends AbstractController
{
    /** @var ProjectService  */
    protected $projectService;

    /** @var ContactService */
    protected $contactService;

    /**
     * ProjectsController constructor.
     * @param $id
     * @param Module $module
     * @param ProjectService $projectService
     * @param ContactService $contactService
     * @param array $config
     */
    public function __construct
    (
        $id,
        Module $module,
        ProjectService $projectService,
        ContactService $contactService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->projectService = $projectService;
        $this->contactService = $contactService;
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
        $projects = $this->projectService->getProjects();
        foreach ($projects as $key => $project) {
            $projects[$key]['contacts'] = $this->contactService->getContacts($project['id']);
        }
        Yii::$app->response->statusCode = 200;

        return $projects;
    }

    /**
     * @return array
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');

        try {
            $project = $this->projectService->getProject($id);
            $project['contacts'] = $this->contactService->getContacts($project['id']);
            Yii::$app->response->statusCode = 200;

            return $project;
        } catch(NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;

            return $this->error($exception->getMessage());
        }
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->post();

        try {
            $project = $this->projectService->createProject($data);
            $project['contacts'] = $this->contactService->getContacts($project['id']);
            Yii::$app->response->statusCode = 201;

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
            $project = $this->projectService->updateProject($id, $data);
            $project['contacts'] = $this->contactService->getContacts($project['id']);
            Yii::$app->response->statusCode = 200;

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
            $this->projectService->deleteProject($id);
            Yii::$app->response->statusCode = 204;
        } catch (NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;

            return $this->error($exception->getMessage());
        }
    }
}
