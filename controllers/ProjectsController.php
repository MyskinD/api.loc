<?php

namespace app\controllers;

use app\services\ProjectsService;
use yii\base\Controller;
use Yii;
use yii\base\Module;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class ProjectsController extends Controller
{
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
     * @param string $message
     * @return array
     */
    protected  function error(string $message): array
    {
        return [
            'error' => $message
        ];
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        return $this->projectsService->getAll();
    }

    /**
     * @return array
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');

        try {

            return $this->projectsService->get($id);
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
            $project = $this->projectsService->add($data);

            return $this->projectsService->get($project->id);
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
            $project = $this->projectsService->save($id, $data);

            return $this->projectsService->get($project->id);
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
            (new ProjectsService())->remove($id);

            return [
                'success' => true
            ];
        } catch (NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;

            return $this->error($exception->getMessage());
        }
    }
}
