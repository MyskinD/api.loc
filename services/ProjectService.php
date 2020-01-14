<?php

namespace app\services;

use app\repositories\ProjectRepository;
use app\repositories\ProjectRepositoryInterface;
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

    /**
     * ProjectService constructor.
     * @param ProjectRepositoryInterface $projectRepository
     * @param ContactService $contactService
     */
    public function __construct
    (
        ProjectRepositoryInterface $projectRepository,
        ContactService $contactService
    ) {
        $this->projectRepository = $projectRepository;
        $this->contactService = $contactService;
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
        if (!$data['contacts']) {

            throw new BadRequestHttpException('There must be at least one contact');
        }
        if (!$data['name'] || !preg_match('/^[a-zA-Z\s]{5,50}$/', $data['name'])) {

            throw new BadRequestHttpException('Enter the correct field NAME');
        }
        if (!$data['code'] || !preg_match('/^[a-z]{3,10}$/', $data['code'])) {

            throw new BadRequestHttpException('Enter the correct field CODE');
        }
        if (!$data['url'] || !filter_var($data['url'], FILTER_VALIDATE_URL)) {

            throw new BadRequestHttpException('Enter the correct field URL');
        }
        if (!$data['budget'] || !filter_var($data['budget'], FILTER_VALIDATE_INT)) {

            throw new BadRequestHttpException('Enter the correct field BUDGET');
        }

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
        if ($data['name'] && !preg_match('/^[a-zA-Z\s]{5,50}$/', $data['name'])) {

            throw new BadRequestHttpException('Enter the correct field NAME');
        }
        if ($data['url'] && !filter_var($data['url'], FILTER_VALIDATE_URL)) {

            throw new BadRequestHttpException('Enter the correct field URL');
        }
        if ($data['budget'] && !filter_var($data['budget'], FILTER_VALIDATE_INT)) {

            throw new BadRequestHttpException('Enter the correct field BUDGET');
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