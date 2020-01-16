<?php

namespace app\validations;

use yii\web\BadRequestHttpException;

class ProjectValidation
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
     * @param $value
     * @return bool
     * @throws BadRequestHttpException
     */
    public function ContactsIsNotNull($value): bool
    {
        if (!$value) {
            throw new BadRequestHttpException('There must be at least one contact');
        }

        return true;
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

    /**
     * @param string $value
     * @param string $field
     * @return bool
     * @throws BadRequestHttpException
     */
    public function UrlIsValid(string $value, string $field): bool
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->error($field);
        }

        return true;
    }

    /**
     * @param string $value
     * @param string $field
     * @return bool
     * @throws BadRequestHttpException
     */
    public function isInteger(string $value, string $field): bool
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->error($field);
        }

        return true;
    }
}