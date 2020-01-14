<?php

namespace app\services;

use app\models\Contacts;
use app\models\Projects;
use app\repositories\ContactRepository;
use app\repositories\ProjectRepository;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Exception;
use Yii;

class ProjectsService
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var ContactRepository */
    private $contactRepository;

    /**
     * ProjectsService constructor.
     * @param ProjectRepository $projectRepository
     * @param ContactRepository $contactRepository
     */
    public function __construct(ProjectRepository $projectRepository, ContactRepository $contactRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->contactRepository = $contactRepository;
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
     * @throws NotFoundHttpException
     */
    public function getProject(int $id): array
    {
        $project = $this->projectRepository->get($id);
        if (is_null($project)) {

            throw new NotFoundHttpException('Project was not found');
        }

        return $project;
    }

    /**
     * @param array $data
     * @return array
     * @throws BadRequestHttpException
     */
    public function setProject(array $data): array
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

        $contacts = [];
        foreach ($data['contacts'] as $contact) {
            if (!$contact['firstName']) {

                throw new BadRequestHttpException('The field firstName must not be empty');
            }
            if (!$contact['lastName']) {

                throw new BadRequestHttpException('The field lastName must not be empty');
            }
            $pattern = '/^\+(\d){3}\s\((\d){2}\)\s(\d){3}-(\d){2}-(\d){2}$/';
            if (!$contact['phone'] || !preg_match($pattern, $contact['phone'])) {

                throw new BadRequestHttpException('Enter the correct field PHONE');
            }
            $contacts[] = [
                'firstName' => $contact['firstName'],
                'lastName' => $contact['lastName'],
                'phone' => $contact['phone'],
            ];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $project = $this->projectRepository->add($data);

            $insertData = [];
            foreach ($contacts as $value) {
                $insertData[] = [
                    $project->id,
                    $value['firstName'],
                    $value['lastName'],
                    $value['phone'],
                ];
            }

            $this->contactRepository->add($insertData);

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
            $this->contactRepository->removeByProjectId($id);
            $this->projectRepository->remove($id);
            $transaction->commit();
        } catch(NotFoundHttpException $exception) {
            $transaction->rollBack();

            throw $exception;
        }
    }
}