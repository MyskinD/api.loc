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

    /** @var \yii\db\Connection */
    protected $db;

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
        $this->db = Yii::$app->db;
    }

    /**
     * @return array
     */
    public function getProjects(): array
    {
        $projects = $this->projectRepository->all();
        foreach ($projects as $key => $project) {
            $projects[$key]['contacts'] = $this->contactService->getContacts($project['id']);
        }

        return $projects;
    }

    /**
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function getProject(int $id): array
    {
        $project = $this->projectRepository->get($id);
        $project['contacts'] = $this->contactService->getContacts($project['id']);

        return $project;
    }

    /**
     * @param array $data
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function createProject(array $data): array
    {
        $this->validation->validateOnCreate($data);

        $transaction = $this->db->beginTransaction();
        try {
            $project = $this->projectRepository->add($data);
            $this->contactService->createContacts($project->id, $data['contacts']);

            $transaction->commit();
        } catch(Exception $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        $project = $this->projectRepository->get($project->id);
        $project['contacts'] = $this->contactService->getContacts($project['id']);

        return $project;
    }

    /**
     * @param int $id
     * @param array $data
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function updateProject(int $id, array $data): array
    {
        $this->validation->validateOnUpdate($data);

        $this->projectRepository->save($id, $data);

        $project = $this->projectRepository->get($id);
        $project['contacts'] = $this->contactService->getContacts($project['id']);

        return $project;
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
        $transaction = $this->db->beginTransaction();
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