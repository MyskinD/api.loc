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
    public function createContacts(int $projectId, array $data):void
    {
        $contacts = [];
        foreach ($data as $contact) {
            $this->validation->isNotNull($contact['firstName'], 'firstName');
            $this->validation->isNotNull($contact['lastName'], 'lastName');
            $this->validation->isNotNull($contact['phone'], 'phone');
            $pattern = '/^\+(\d){3}\s\((\d){2}\)\s(\d){3}-(\d){2}-(\d){2}$/';
            $this->validation->isValid($pattern, $contact['phone'], 'phone');

            $contacts[] = [
                'firstName' => $contact['firstName'],
                'lastName' => $contact['lastName'],
                'phone' => $contact['phone'],
            ];
        }

        $insertData = [];
        foreach ($contacts as $value) {
            $insertData[] = [
                $projectId,
                $value['firstName'],
                $value['lastName'],
                $value['phone'],
            ];
        }

        $this->contactRepository->batchAdd($insertData);
    }

    /**
     * @param int $projectId
     */
    public function removeContactsByProjectId(int $projectId):void
    {
        $this->contactRepository->removeByProjectId($projectId);
    }
}