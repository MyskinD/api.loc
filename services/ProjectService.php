<?php

namespace app\services;

use app\repositories\ProjectRepository;
use app\repositories\ProjectRepositoryInterface;
use app\validations\ProjectValidation;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Exception;
use Yii;

class ProjectService
{
    /** @var ProjectRepository */
    protected $projectRepository;

    /** @var ContactService */
    protected $contactService;

    /** @var ProjectValidation */
    protected $validation;

    /**
     * ProjectService constructor.
     * @param ProjectRepositoryInterface $projectRepository
     * @param ContactService $contactService
     * @param ProjectValidation $projectValidation
     */
    public function __construct
    (
        ProjectRepositoryInterface $projectRepository,
        ContactService $contactService,
        ProjectValidation $projectValidation
    ) {
        $this->projectRepository = $projectRepository;
        $this->contactService = $contactService;
        $this->validation = $projectValidation;
    }

    /**
     * @return array
     */
    public function getProjects(): array
    {
        return $this->projectRepository->all();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getProject(int $id): array
    {
        $project = $this->projectRepository->get($id);

        return $project;
    }

    /**
     * @param array $data
     * @return array
     * @throws BadRequestHttpException
     */
    public function createProject(array $data): array
    {
        $this->validation->ContactsIsNotNull($data['contacts']);
        $this->validation->isNotNull($data['name'], 'name');
        $this->validation->isValid('/^[a-zA-Z\s]{5,50}$/', $data['name'], 'name');
        $this->validation->isNotNull($data['code'], 'code');
        $this->validation->isValid('/^[a-z]{3,10}$/', $data['code'], 'code');
        $this->validation->isNotNull($data['url'], 'url');
        $this->validation->UrlIsValid($data['url'], 'url');
        $this->validation->isNotNull($data['budget'], 'budget');
        $this->validation->isInteger($data['budget'], 'budget');

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $project = $this->projectRepository->add($data);
            $this->contactService->createContacts($project->id, $data['contacts']);

            $transaction->commit();
        } catch(Exception $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        return $this->projectRepository->get($project->id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return array
     * @throws BadRequestHttpException
     */
    public function updateProject(int $id, array $data): array
    {
        if ($data['name']) {
            $this->validation->isValid('/^[a-zA-Z\s]{5,50}$/', $data['name'], 'name');
        }
        if ($data['url']) {
            $this->validation->UrlIsValid($data['url'], 'url');
        }
        if ($data['budget']) {
            $this->validation->isInteger($data['budget'], 'budget');
        }

        $this->projectRepository->save($id, $data);

        return $this->projectRepository->get($id);
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteProject(int $id): void
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $this->contactService->removeContactsByProjectId($id);
            $this->projectRepository->remove($id);
            $transaction->commit();
        } catch(NotFoundHttpException $exception) {
            $transaction->rollBack();

            throw $exception;
        }
    }
}