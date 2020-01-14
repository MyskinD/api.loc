<?php

namespace app\services;


use app\repositories\ContactRepository;
use app\repositories\ContactRepositoryInterface;

class ContactService
{
    /** @var ContactRepository */
    protected $contactRepository;

    /**
     * ContactService constructor.
     * @param ContactRepositoryInterface $contactRepository
     */
    public function __construct(ContactRepositoryInterface $contactRepository) {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @param int $projectId
     * @param array $data
     * @throws \yii\db\Exception
     */
    public function createContacts(int $projectId, array $data):void
    {
        $contacts = [];
        foreach ($data as $contact) {
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