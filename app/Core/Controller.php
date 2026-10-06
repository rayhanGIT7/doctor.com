<?php

namespace App\Core;

abstract class Controller
{
    protected string $layout = 'main';

    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data, $this->layout);
    }

    protected function redirect(string $path, array $query = []): void
    {
        header('Location: ' . url($path, $query));
        exit;
    }

    // Go back to the previous page (used after a failed form submit)
    protected function back(): void
    {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('/')));
        exit;
    }

    // Save errors + old input in the session and go back to the form
    protected function backWithErrors(array $errors): void
    {
        Session::withErrors($errors, $_POST);
        $this->back();
    }

    protected function success(string $message): void
    {
        Session::flash('success', $message);
    }

    protected function error(string $message): void
    {
        Session::flash('danger', $message);
    }

    // Trimmed value from the submitted form
    protected function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    // Trimmed value from the URL query string
    protected function query(string $key, string $default = ''): string
    {
        $value = $_GET[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    protected function page(): int
    {
        return max(1, (int) ($_GET['page'] ?? 1));
    }
}
