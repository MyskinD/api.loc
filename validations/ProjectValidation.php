<?php

namespace app\validations;

use yii\web\BadRequestHttpException;

class ProjectValidation extends Validation
{
    /**
     * @param array $data
     * @throws BadRequestHttpException
     */
    public function validateOnCreate(array $data): void
    {
        $this->isContactsNotNull($data['contacts']);
        $this->isNull($data['name'], 'name');
        $this->isRegExp('/^[a-zA-Z\s]{5,50}$/', $data['name'], 'name');
        $this->isNull($data['code'], 'code');
        $this->isRegExp('/^[a-z]{3,10}$/', $data['code'], 'code');
        $this->isNull($data['url'], 'url');
        $this->isUrl($data['url'], 'url');
        $this->isNull($data['budget'], 'budget');
        $this->isInt($data['budget'], 'budget');
    }

    /**
     * @param array $data
     * @throws BadRequestHttpException
     */
    public function validateOnUpdate(array $data): void
    {
        if ($data['code']) {
            $this->isNotNull($data['code'], 'code');
        }
        if ($data['name']) {
            $this->isRegExp('/^[a-zA-Z\s]{5,50}$/', $data['name'], 'name');
        }
        if ($data['url']) {
            $this->isUrl($data['url'], 'url');
        }
        if ($data['budget']) {
            $this->isInt($data['budget'], 'budget');
        }
    }

    /**
     * @param array $value
     * @throws BadRequestHttpException
     */
    protected function isContactsNotNull(array $value): void
    {
        if (!$value) {
            throw new BadRequestHttpException('There must be at least one contact');
        }
    }
}