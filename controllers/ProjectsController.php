<?php

namespace app\controllers;

use app\models\Projects;
use yii\base\Controller;
use Yii;


class ProjectsController extends Controller
{
    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    function actionIndex()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return Projects::getProjects();
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    function actionView()
    {
        $id = Yii::$app->request->get('id');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return Projects::getProject($id);
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    function actionCreate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        if (!$data['contacts']) {
            return [
                'typeMessage' => 'error',
                'message' => 'There must be at least one contact'
            ];
        }

        $project['name'] = $data['name'] &&
            preg_match('/^[a-zA-Z\s]{5,50}$/', $data['name'])
                ? $data['name']
                : null
        ;
        $project['code'] = $data['code'] &&
            preg_match('/^[a-z]{3,10}$/', $data['code'])
                ? $data['code']
                : null
        ;
        $project['url'] = $data['url'] &&
            filter_var($data['url'], FILTER_VALIDATE_URL)
                ? $data['url']
                : null
        ;
        $project['budget'] = $data['budget'] &&
            filter_var($data['budget'], FILTER_VALIDATE_INT)
                ? $data['budget']
                : null
        ;

        $i = 0;
        $contacts = [];
        foreach ($data['contacts'] as $contact) {
            $contacts[$i]['firstName'] = $contact['firstName']
                ? $contact['firstName']
                : null
            ;
            $contacts[$i]['lastName'] = $contact['lastName']
                ? $contact['lastName']
                : null
            ;
            $contacts[$i]['phone'] = $contact['phone'] &&
                preg_match('/^\+(\d){3}\s\((\d){2}\)\s(\d){3}-(\d){2}-(\d){2}$/', $contact['phone'])
                    ? $contact['phone']
                    : null
            ;
            $i++;
        }

        try {
            $id = Projects::_create($project, $contacts);
        } catch (\Exception $e) {

            return [
                'typeMessage' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return Projects::getProject($id);
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    function actionUpdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->get('id');
        $data = Yii::$app->request->post();

        $project['name'] = $data['name'] && preg_match('/^[a-zA-Z\s]{5,50}$/', $data['name'])
            ? $data['name']
            : null
        ;
        $project['url'] = $data['url'] && filter_var($data['url'], FILTER_VALIDATE_URL)
            ? $data['url']
            : null
        ;
        $project['budget'] = $data['budget'] && filter_var($data['budget'], FILTER_VALIDATE_INT)
            ? $data['budget']
            : null
        ;

        try {
            if (!$id = Projects::_update($id, $project)) {

                return [
                    'typeMessage' => 'error',
                    'message' => 'Project could not be changed'
                ];
            }
        } catch (\Exception $e) {

            return [
                'typeMessage' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return Projects::getProject($id);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    function actionDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->get('id');

        try {
            Projects::_delete($id);
        } catch (\Exception $e) {

            return [
                'typeMessage' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return [
            'typeMessage' => 'success',
            'message' => 'OK'
        ];
    }
}
