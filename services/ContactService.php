<?php

namespace app\services;

use app\repositories\ContactRepository;
use app\repositories\ContactRepositoryInterface;
use app\validations\ContactValidation;

class ContactService
{
    /** @var ContactRepository */
    protected $contactRepository;

    /** @var ContactValidation */
    protected $validation;

    /**
     * ContactService constructor.
     * @param ContactRepositoryInterface $contactRepository
     * @param ContactValidation $contactValidation
     */
    public function __construct(
        ContactRepositoryInterface $contactRepository,
        ContactValidation $contactValidation
    ) {
        $this->contactRepository = $contactRepository;
        $this->validation = $contactValidation;
    }

    /**
     * @param int $projectId
     * @param array $data
     * @throws \yii\db\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function createContacts(int $projectId, array $data): void
    {
        $contacts = [];
        foreach ($data as $contact) {
            $this->validation->validateOnCreate($contact);
            $contacts[] = [
                $projectId,
                $contact['firstName'],
                $contact['lastName'],
                $contact['phone'],
            ];
        }

        $this->contactRepository->batchAdd($contacts);
    }

    /**
     * @param int $projectId
     */
    public function removeContactsByProjectId(int $projectId): void
    {
        $this->contactRepository->removeByProjectId($projectId);
    }

    /**
     * @param int $projectId
     * @return array
     */
    public function getContacts(int $projectId): array
    {
        $contacts = $this->contactRepository->getByProjectId($projectId);

        return $contacts;
    }
}