<?php

declare(strict_types=1);

/**
 * Рендерит шаблон и возвращает HTML-строку.
 *
 * @param string $template Путь к шаблону относительно View/ (без .php)
 * @param array $data Переменные для передачи в шаблон
 * @return string HTML-контент
 */
function render(string $template, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    require BASE_PATH . "/src/Presentation/View/{$template}.php";
    return ob_get_clean();
}
function buildFilterUrl(array $filters, array $overrides = []): string
{
    $merged = array_merge($filters, $overrides);

    $params = [];
    foreach ($merged as $key => $value) {
        if ($value === null || $value === '' || $value === 0) {
            continue;
        }
        $params[$key] = $value;
    }

    return '/books' . ($params === [] ? '' : '?' . http_build_query($params));
}