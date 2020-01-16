<?php

namespace app\validations;


abstract class Validation
{
    /**
     * @param string $value
     * @param string $field
     * @throws BadRequestHttpException
     */
    protected function isNull(string $value, string $field): void
    {
        if (!$value) {
            throw new BadRequestHttpException('The field `' . mb_strtoupper($field) . '` must not be empty');
        }
    }

    /**
     * @param string $pattern
     * @param string $value
     * @param string $field
     * @throws BadRequestHttpException
     */
    protected function isRegExp(string $pattern, string $value, string $field): void
    {
        if (!preg_match($pattern, $value)) {
            throw new BadRequestHttpException('Enter the correct field `' . mb_strtoupper($field) . '`');
        }
    }

    /**
     * @param string $value
     * @param string $field
     * @throws BadRequestHttpException
     */
    protected function isNotNull(string $value, string $field): void
    {
        if ($value) {
            throw new BadRequestHttpException('The field `' . mb_strtoupper($field) . '` can not be changed');
        }
    }

    /**
     * @param string $value
     * @param string $field
     * @throws BadRequestHttpException
     */
    protected function isUrl(string $value, string $field): void
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new BadRequestHttpException('Enter the correct field `' . mb_strtoupper($field) . '`');
        }
    }

    /**
     * @param string $value
     * @param string $field
     * @throws BadRequestHttpException
     */
    protected function isInt(string $value, string $field): void
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            throw new BadRequestHttpException('Enter the correct field `' . mb_strtoupper($field) . '`');
        }
    }
}