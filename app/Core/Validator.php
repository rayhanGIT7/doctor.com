<?php

namespace App\Core;

use DateTime;

/**
 * Simple form validator.
 *
 *   $v = new Validator($_POST);
 *   $v->required('name', 'email')->email('email')->max('name', 100);
 *   if ($v->fails()) { ... $v->errors() ... }
 *
 * Only the first error of each field is kept.
 * Rules other than required() are skipped when the field is empty.
 */
class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string ...$fields): self
    {
        foreach ($fields as $field) {
            if ($this->value($field) === '') {
                $this->addError($field, $this->label($field) . ' is required.');
            }
        }

        return $this;
    }

    public function email(string $field): self
    {
        $value = $this->value($field);
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'Please enter a valid email address.');
        }

        return $this;
    }

    // Digits only, 10-15 long, optional leading "+" (e.g. 01712345678 or +8801712345678)
    public function phone(string $field): self
    {
        $value = $this->value($field);
        if ($value !== '' && !preg_match('/^\+?[0-9]{10,15}$/', $value)) {
            $this->addError($field, 'Please enter a valid phone number, e.g. 01712345678.');
        }

        return $this;
    }

    public function min(string $field, int $length): self
    {
        $value = $this->value($field);
        if ($value !== '' && $this->length($value) < $length) {
            $this->addError($field, $this->label($field) . " must be at least $length characters.");
        }

        return $this;
    }

    public function max(string $field, int $length): self
    {
        $value = $this->value($field);
        if ($value !== '' && $this->length($value) > $length) {
            $this->addError($field, $this->label($field) . " may not be longer than $length characters.");
        }

        return $this;
    }

    public function number(string $field, float $min, float $max): self
    {
        $value = $this->value($field);
        if ($value !== '' && (!is_numeric($value) || $value < $min || $value > $max)) {
            $this->addError($field, $this->label($field) . " must be a number between $min and $max.");
        }

        return $this;
    }

    // Format: 2026-10-10
    public function date(string $field): self
    {
        $value = $this->value($field);
        if ($value !== '' && !$this->matchesFormat($value, 'Y-m-d')) {
            $this->addError($field, 'Please enter a valid date.');
        }

        return $this;
    }

    // Format: 17:30
    public function time(string $field): self
    {
        $value = $this->value($field);
        if ($value !== '' && !$this->matchesFormat($value, 'H:i')) {
            $this->addError($field, 'Please enter a valid time.');
        }

        return $this;
    }

    public function in(string $field, array $allowed): self
    {
        $value = $this->value($field);
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->addError($field, 'Please select a valid ' . strtolower($this->label($field)) . '.');
        }

        return $this;
    }

    public function same(string $field, string $otherField): self
    {
        if ($this->value($field) !== $this->value($otherField)) {
            $this->addError($field, $this->label($field) . ' does not match.');
        }

        return $this;
    }

    public function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function value(string $field): string
    {
        $value = $this->data[$field] ?? '';

        return is_string($value) ? trim($value) : '';
    }

    // "patient_phone" becomes "Patient phone"
    private function label(string $field): string
    {
        return ucfirst(str_replace('_', ' ', $field));
    }

    // Counts characters (not bytes), so Bangla text is measured correctly
    private function length(string $value): int
    {
        return (int) preg_match_all('/./us', $value);
    }

    private function matchesFormat(string $value, string $format): bool
    {
        $date = DateTime::createFromFormat($format, $value);

        return $date !== false && $date->format($format) === $value;
    }
}
