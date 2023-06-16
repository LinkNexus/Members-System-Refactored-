<?php

class Validator
{

    protected array $data, $errors;

    public function __construct(array $data)
    {

        $this->data = $data;

    }

    protected function getField(string $field): ?string
    {

        if (!isset($this->data[$field])){
            return null;
        }

        return $this->data[$field];

    }

    public function isAlphanumeric(string $field, string $errorMsg): void
    {

        if(!preg_match('/^[a-z0-9A-Z_]+$/', $this->getField($field))){
            $this->errors[$field] = $errorMsg;
        }

    }

    public function isEmail(string $field, string $errorMsg): void
    {

        if (!filter_var($this->getField($field), FILTER_VALIDATE_EMAIL)){
            $this->errors[$field] = $errorMsg;
        }

    }

    public function isUnique(string $field, Database $link, string $table, string $errorMsg): void
    {

        $product = $link->query('SELECT id FROM '. $table .' WHERE username = :username', ['username' => $this->getField($field)])->fetch();
        if ($product){
            $this->errors[$field] = $errorMsg;
        }

    }

    public function isConfirmed(array $fields, array $errorMessages): void
    {

        if (strlen($this->getField($fields[0])) < 5){
            $this->errors[$fields[0]] = $errorMessages[0];
        }

        if ($this->getField($fields[0]) != $this->getField($fields[1])){
            $this->errors[$fields[1]] = $errorMessages[1];
        }

    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

}