<?php

namespace app\controllers;

use app\models\Projects;
use yii\base\Controller;
use Yii;


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
        return [
            'success' => true,
            'data' => Projects::getProjects(),
            'error' => null
        ];
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    function actionView()
    {
        $id = Yii::$app->request->get('id');

        return [
            'success' => true,
            'data' => Projects::getProject($id),
            'error' => null
        ];
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    function actionCreate()
    {
        $data = Yii::$app->request->post();

        try {
            $id = Projects::make($data);
        } catch (\Exception $e) {

            return [
                'success' => false,
                'data' => [],
                'error' => $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'data' => Projects::getProject($id),
            'error' => null
        ];
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
        } catch (\Exception $e) {

            return [
                'success' => false,
                'data' => [],
                'error' => $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'data' => Projects::getProject($id),
            'error' => null
        ];
    }

    /**
     * @return array
     * @throws \Throwable
     */
    function actionDelete()
    {
        $id = Yii::$app->request->get('id');

        try {
            Projects::remove($id);
        } catch (\Exception $e) {

            return [
                'success' => false,
                'data' => [],
                'error' => $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'data' => [],
            'error' => null
        ];
    }
}
