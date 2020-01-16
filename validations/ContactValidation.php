<?php

namespace app\validations;

use yii\web\BadRequestHttpException;

class ContactValidation
{
    /**
     * @param $field
     * @throws BadRequestHttpException
     */
    private function error($field)
    {
        throw new BadRequestHttpException('Enter the correct field `' . mb_strtoupper($field) . '`');
    }

    /**
     * @param string $value
     * @param string $field
     * @return bool
     * @throws BadRequestHttpException
     */
    public function isNotNull(string $value, string $field): bool
    {
        if (!$value) {
            throw new BadRequestHttpException('The field `' . mb_strtoupper($field) . '` must not be empty');
        }

        return true;
    }

    /**
     * @param string $pattern
     * @param string $value
     * @param string $field
     * @return bool
     * @throws BadRequestHttpException
     */
    public function isValid(string $pattern, string $value, string $field): bool
    {
        if (!preg_match($pattern, $value)) {
            $this->error($field);
        }

        return true;
    }
}