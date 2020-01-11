<?php

namespace app\controllers;

use app\services\ProjectsService;
use yii\base\Controller;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

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
    public function actionIndex()
    {
        $model = new ProjectsService();

        return $model->all();
    }

    /**
     * @return array
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');

        try {
            $model = new ProjectsService();

            return $model->get($id);
        } catch(NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;

            return [
                'error' => $exception->getMessage()
            ];
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
            $model = new ProjectsService();

            return $model->get($model->add($data));
        } catch (BadRequestHttpException $exception) {
            Yii::$app->response->statusCode = 400;

            return [
                'error' => $exception->getMessage()
            ];
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
            $model = new ProjectsService();

            return $model->get($model->save($id, $data));
        } catch (NotFoundHttpException $exception) {
            Yii::$app->response->statusCode = 404;

            return [
                'error' => $exception->getMessage()
            ];
        } catch (BadRequestHttpException $exception) {
            Yii::$app->response->statusCode = 400;

            return [
                'error' => $exception->getMessage()
            ];
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

            return [
                'error' => $exception->getMessage()
            ];
        }
    }
}
