<?php

namespace app\controllers;

use app\models\Projects;
use yii\base\Controller;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

class ProjectsController extends Controller
{
    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    function actionIndex()
    {
        Yii::$app->response->statusCode = 200;
        Yii::$app->response->data = Projects::getProjects();
    }

    function actionView()
    {
        $id = Yii::$app->request->get('id');

        try {
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->data = Projects::getProject($id);
        } catch(NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = ['error' => $exception->getMessage()];
        }
    }

    function actionCreate()
    {
        $data = Yii::$app->request->post();

        try {
            $id = Projects::make($data);
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->data = Projects::getProject($id);
        } catch (BadRequestHttpException $exception) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->data = ['error' => $exception->getMessage()];
        }
    }

    function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $data = Yii::$app->request->post();

        try {
            $id = Projects::change($id, $data);
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->data = Projects::getProject($id);
        } catch (NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = ['error' => $exception->getMessage()];
        } catch (BadRequestHttpException $exception) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->data = ['error' => $exception->getMessage()];
        }
    }

    /**
     * @return array
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    function actionDelete()
    {
        $id = Yii::$app->request->get('id');

        try {
            Projects::remove($id);
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->data = [];
        } catch (NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = ['error' => $exception->getMessage()];
        }
    }
}
