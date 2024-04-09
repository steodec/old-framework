<?php

namespace Humbrain\Framework\Validator;

use Humbrain\Framework\database\Repository;

class Validator
{
    private array $params;

    /** @var ValidationError[] */
    private array $errors = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param string ...$keys
     * @return $this
     */
    public function required(string ...$keys): static
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (empty($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    private function getValue(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    /**
     * @param string $key
     * @param string $rules
     * @param array|null $attributes
     * @return void
     */
    private function addError(string $key, string $rules, ?array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rules, $attributes);
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function notEmpty(string $string): static
    {
        $value = $this->params[$string];
        if (empty($value)) {
            $this->addError($string, 'empty');
        }
        return $this;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function slug(string $string): static
    {
        $value = $this->params[$string];
        $pattern = '/^([a-z0-9]+-?)+$/';
        if (!empty($value) && !preg_match($pattern, $this->params[$string])) {
            $this->addError($string, 'slug');
        }
        return $this;
    }

    public function length(string $key, ?int $min = null, ?int $max = null): static
    {
        $value = $this->params[$key];
        $length = mb_strlen($value);
        if (!is_null($min) && !is_null($max) && ($length < $min || $length > $max)) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) && $length < $min) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) && $length > $max) {
            $this->addError($key, 'maxLength', [$max]);
            return $this;
        }
        return $this;
    }

    public function dateTime(string $key, ?string $format = 'Y-m-d H:i:s'): static
    {
        $value = $this->params[$key];
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if (!is_bool($errors) && ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false)) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }

    public function isValidate(): bool
    {
        return empty($this->errors);
    }

    public function unique(string $key, Repository $repository, ?int $exclude = null): static
    {
        $value = $this->params[$key];
        if ($repository->exists($value, $key, $exclude)) {
            $this->addError($key, 'unique', [$value, $key]);
        }
        return $this;
    }

    public function exists(string $key, Repository $repository): static
    {
        $value = $this->params[$key];
        if (!$repository->exists($value)) {
            $this->addError($key, 'exists', [$value]);
        }
        return $this;
    }
}
