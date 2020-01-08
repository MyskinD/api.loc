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

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    function actionIndex()
    {
        try {

            return Projects::getProjects();
        } catch(NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = ['error' => $exception->getMessage()];
        }
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    function actionView()
    {
        $id = Yii::$app->request->get('id');

        try {

            return Projects::getProject($id);
        } catch(NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = ['error' => $exception->getMessage()];
        }
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    function actionCreate()
    {
        $data = Yii::$app->request->post();

        try {
            $id = Projects::make($data);

            return Projects::getProject($id);
        } catch (BadRequestHttpException $exception) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->data = ['error' => $exception->getMessage()];
        }
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $data = Yii::$app->request->post();

        try {
            $id = Projects::change($id, $data);

            return Projects::getProject($id);
        } catch (NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;
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

            return [];
        } catch (NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;
            Yii::$app->response->data = ['error' => $exception->getMessage()];
        }
    }
}
