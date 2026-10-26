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