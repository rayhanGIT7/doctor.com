<?php

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\View;

// ---------- App ----------

function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require BASE_PATH . '/config/config.php';
    }

    return $config[$key] ?? $default;
}

function auth(): ?array
{
    return Auth::user();
}

// Show an error page and stop
function abort(int $code, string $message = ''): void
{
    $messages = [
        403 => 'You do not have permission to view this page.',
        404 => 'The page you are looking for was not found.',
        419 => 'Your session has expired. Please refresh and try again.',
    ];

    http_response_code($code);
    View::render('errors/error', ['code' => $code, 'message' => $message ?: ($messages[$code] ?? 'Error')], 'error');
    exit;
}

function today(): string
{
    return date('Y-m-d');
}

// ---------- URLs ----------

// Folder the app runs from, e.g. "" or "/doctor.com/public"
function base_url(): string
{
    static $base = null;
    if ($base === null) {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');

        // Opened as http://localhost/doctor.com/ : the root .htaccess hides "/public" from the URL
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (substr($base, -7) === '/public' && strpos($uri, $base . '/') !== 0 && $uri !== $base) {
            $base = substr($base, 0, -7);
        }
    }

    return $base;
}

function url(string $path = '', array $query = []): string
{
    $url = base_url() . '/' . ltrim($path, '/');

    // Drop empty filters so URLs stay clean
    $query = array_filter($query, fn ($value) => $value !== '' && $value !== null);

    return $query ? $url . '?' . http_build_query($query) : $url;
}

function asset(string $path): string
{
    return url('assets/' . $path);
}

// Current path without the base folder, e.g. "/admin/doctors"
function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = base_url();

    if ($base !== '' && strpos($path, $base) === 0) {
        $path = substr($path, strlen($base));
    }

    return '/' . trim($path, '/');
}

// "active" when the menu link matches the current page
function nav_active(string $path, bool $exact = false): string
{
    $path    = '/' . trim($path, '/');
    $current = current_path();

    $isActive = $exact ? $current === $path : ($current === $path || strpos($current, $path . '/') === 0);

    return $isActive ? 'active' : '';
}

// Current query string with some values replaced (used by pagination)
function query_with(array $changes): array
{
    return array_merge($_GET, $changes);
}

// ---------- Output ----------

// Escape text for HTML (always use this when printing data)
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function partial(string $view, array $data = []): string
{
    return View::partial($view, $data);
}

function money($amount): string
{
    return '৳' . number_format((float) $amount);
}

function format_date(string $date): string
{
    return date('d M Y', strtotime($date));
}

function format_time(string $time): string
{
    return date('g:i A', strtotime($time));
}

function day_name(int $day): string
{
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    return $days[$day] ?? '';
}

function status_label(string $status): string
{
    return ucwords(str_replace('_', ' ', $status));
}

function status_badge(string $status): string
{
    $colors = [
        'active'    => 'success',
        'inactive'  => 'secondary',
        'confirmed' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger',
        'no_show'   => 'warning',
    ];
    $color = $colors[$status] ?? 'secondary';

    return '<span class="badge rounded-pill text-bg-' . $color . '">' . e(status_label($status)) . '</span>';
}

function initials(string $name): string
{
    $name  = preg_replace('/^(dr\.?|prof\.?)\s+/i', '', trim($name));
    $parts = preg_split('/\s+/', $name);
    $first = substr($parts[0] ?? '', 0, 1);
    $last  = count($parts) > 1 ? substr(end($parts), 0, 1) : '';

    return strtoupper($first . $last);
}

// Doctor photo, or a circle with initials when there is no photo
function avatar(?string $image, string $name, int $size = 64): string
{
    $style = "width:{$size}px;height:{$size}px";

    if ($image) {
        return '<img src="' . e(url('uploads/' . $image)) . '" alt="' . e($name) . '" class="avatar" style="' . $style . '">';
    }

    $fontSize = (int) ($size * 0.38);

    return '<span class="avatar avatar-initials" style="' . $style . ';font-size:' . $fontSize . 'px">' . e(initials($name)) . '</span>';
}

// ---------- Forms ----------

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">';
}

// Previous input after a failed submit, otherwise $default
function old(string $key, $default = ''): string
{
    $old = Session::old();

    return array_key_exists($key, $old) && is_string($old[$key]) ? $old[$key] : (string) ($default ?? '');
}

// Same as old() but for checkbox lists like categories[]
function old_array(string $key, array $default = []): array
{
    $old = Session::old();

    if (empty($old)) {
        return $default;
    }

    return isset($old[$key]) && is_array($old[$key]) ? $old[$key] : [];
}

function error(string $key): string
{
    return Session::errors()[$key] ?? '';
}

// Bootstrap class for a field with an error
function invalid(string $key): string
{
    return error($key) !== '' ? ' is-invalid' : '';
}

function field_error(string $key): string
{
    $message = error($key);

    return $message !== '' ? '<div class="invalid-feedback">' . e($message) . '</div>' : '';
}

function selected($value, $current): string
{
    return (string) $value === (string) $current ? 'selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? 'checked' : '';
}
