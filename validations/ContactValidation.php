<?php

namespace app\validations;

class ContactValidation extends Validation
{
    /**
     * @param array $data
     * @throws \yii\web\BadRequestHttpException
     */
    public function validateOnCreate(array $data): void
    {
        $this->isNull($data['firstName'], 'firstName');
        $this->isNull($data['lastName'], 'lastName');
        $this->isNull($data['phone'], 'phone');
        $pattern = '/^\+(\d){3}\s\((\d){2}\)\s(\d){3}-(\d){2}-(\d){2}$/';
        $this->isRegExp($pattern, $data['phone'], 'phone');
    }
}