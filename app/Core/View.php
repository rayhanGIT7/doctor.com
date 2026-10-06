<?php

namespace App\Core;

class View
{
    // Renders a view inside a layout. The view output is available as $content in the layout.
    public static function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $data['content'] = self::partial($view, $data);

        echo self::partial('layouts/' . $layout, $data);
    }

    // Renders a single view file and returns the HTML
    public static function partial(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require BASE_PATH . '/views/' . $view . '.php';

        return ob_get_clean();
    }
}
