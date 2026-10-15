<?php

declare(strict_types=1);

namespace App\Infrastructure\Debug;

/**
 * Инструмент отладки: консоль, файл, HTML-дамп, таймеры.
 *
 * Не должен использоваться в Domain-слое.
 * Все пути строятся от BASE_PATH, а не от DOCUMENT_ROOT —
 * это делает класс пригодным и для CLI, и для веба.
 */
final class Debug
{
    /** @var array<int|string, array{start:int, end:int, time:float}> */
    private static array $time = [];

    /**
     * Дописать сообщение с меткой в файл logs/YYYY/MM/DD/log.txt (или указанный файл).
     *
     * @param mixed  $message
     * @param string $label
     * @param string $fileName Имя файла внутри папки дня, по умолчанию log.txt
     */
    public static function lm(mixed $message, string $label = '', string $fileName = ''): void
    {
        $dir = self::buildLogDir();

        $file = $dir . ($fileName === '' ? 'log.txt' : $fileName);

        $line = \sprintf(
            "%s\t%s: %s (line: %d, file: %s)%s",
            \date('r'),
            $label,
            \print_r($message, true),
            self::callerLine(),
            self::callerFile(),
            \PHP_EOL,
        );

        \file_put_contents($file, $line, \FILE_APPEND | \LOCK_EX);
    }

    /**
     * Рекурсивно превращает объект (или массив объектов) в массив.
     */
    public static function ota(mixed $object): mixed
    {
        if (\is_object($object)) {
            $object = \get_object_vars($object);
        }

        if (\is_array($object)) {
            return \array_map(self::ota(...), $object);
        }

        return $object;
    }

    /**
     * Вывести переменную в консоль браузера.
     *
     * @param bool $useBackTrace Добавлять строку с file:line вызывающего кода
     * @param bool $toString     Вернуть HTML-строку вместо вывода
     */
    public static function dtc(
        mixed $var,
        string $label = '',
        bool $useBackTrace = true,
        bool $toString = false,
    ): string {
        $payload = self::encode($var);

        $labelPart = $label === ''
            ? ''
            : \json_encode($label . ':', \JSON_UNESCAPED_UNICODE) . ',';

        $backPart = '';
        if ($useBackTrace) {
            $backPart = \sprintf(
                'console.log(%s);',
                \json_encode(
                    \sprintf('line: %d, file: %s', self::callerLine(), self::callerFile()),
                    \JSON_UNESCAPED_UNICODE,
                ),
            );
        }

        $js = '<script>console.log(' . $labelPart . $payload . ');' . $backPart . '</script>';

        if ($toString) {
            return $js;
        }

        echo $js;

        return '';
    }

    /**
     * Сохранить данные в JSON-файл в корне проекта.
     */
    public static function sjs(mixed $data, string $fileName = 'data'): int|false
    {
        $path = self::rootPath() . '/' . self::sanitizeFileName($fileName) . '.json';

        return \file_put_contents(
            $path,
            \json_encode($data, \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT),
            \LOCK_EX,
        );
    }

    /**
     * Прочитать данные из JSON-файла.
     */
    public static function gjs(string $fileName = 'data'): mixed
    {
        $path = self::rootPath() . '/' . self::sanitizeFileName($fileName) . '.json';

        if (!\is_file($path)) {
            return false;
        }

        $raw = \file_get_contents($path);

        return \json_decode($raw, true);
    }

    /**
     * HTML-дамп переменной в раскрывающемся блоке.
     */
    public static function d(
        mixed $value,
        string $label,
        string $color = '#000',
        bool $collapse = true,
    ): void {
        $rand = \random_int(1, \PHP_INT_MAX);
        $labelEsc = \htmlspecialchars($label, \ENT_QUOTES, 'UTF-8');
        $colorEsc = \htmlspecialchars($color, \ENT_QUOTES, 'UTF-8');

        $labelId = 'label_' . $rand;
        $blockId = 'block_' . $rand;

        echo '<div style="border-bottom: silver 1px solid;margin-bottom: 5px;padding-bottom:5px;color:' . $colorEsc . ';">'
            . '<div' . ($collapse ? ' id="' . $labelId . '"' : '') . ' style="cursor:pointer;">' . $labelEsc . '</div>'
            . '<pre' . ($collapse ? ' id="' . $blockId . '" hidden' : '') . '>'
            . \htmlspecialchars(\print_r($value, true), \ENT_QUOTES, 'UTF-8')
            . '</pre>';

        if ($collapse) {
            echo '<script>(function(){var l=document.getElementById(' . \json_encode($labelId) . ');'
                . 'var b=document.getElementById(' . \json_encode($blockId) . ');'
                . 'l.addEventListener("click",function(){'
                . 'if(b.hasAttribute("hidden")){b.removeAttribute("hidden");}else{b.setAttribute("hidden","hidden");}'
                . '});})();</script>';
        }

        echo '</div>';
    }

    //region Time debug

    public static function start(int|string $id = 0): void
    {
        self::$time[$id] = ['start' => \hrtime(true), 'end' => 0, 'time' => 0.0];
    }

    /**
     * @return array{start:int, end:int, time:float} Время в наносекундах
     */
    public static function stop(int|string $id = 0): array
    {
        if (!isset(self::$time[$id])) {
            return ['start' => 0, 'end' => 0, 'time' => 0.0];
        }

        self::$time[$id]['end'] = \hrtime(true);
        self::$time[$id]['time'] = (float) (self::$time[$id]['end'] - self::$time[$id]['start']);

        return self::$time[$id];
    }

    //endregion

    //region Internals

    /**
     * Безопасно кодирует значение в JSON для вставки в JS.
     */
    private static function encode(mixed $var): string
    {
        $encoded = \json_encode(
            $var,
            \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT | \JSON_PARTIAL_OUTPUT_ON_ERROR,
        );

        if ($encoded === false) {
            return \json_encode('[unserializable: ' . \get_debug_type($var) . ']');
        }

        return $encoded;
    }

    /**
     * Номер строки вызывающего кода (на уровень выше самого метода Debug).
     *
     * Стек: [0] callerLine(), [1] публичный метод Debug (dtc/lm/d),
     * а метаданные file/line в [1] — это место вызова из твоего кода.
     */
    private static function callerLine(): int
    {
        $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        return $trace[1]['line'] ?? 0;
    }

    private static function callerFile(): string
    {
        $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        return $trace[1]['file'] ?? 'unknown';
    }

    private static function rootPath(): string
    {
        // BASE_PATH задаётся в public/index.php: define('BASE_PATH', dirname(__DIR__));
        if (\defined('BASE_PATH')) {
            return \constant('BASE_PATH');
        }

        // Фолбэк для CLI/тестов
        return \dirname(__DIR__, 3);
    }

    private static function buildLogDir(): string
    {
        $dir = self::rootPath() . '/logs/' . \date('Y/m/d');

        if (!\is_dir($dir) && !\mkdir($dir, 0700, true) && !\is_dir($dir)) {
            throw new \RuntimeException("Cannot create log directory: $dir");
        }

        // Защита от просмотра логов через веб (пишем один раз)
        $htaccess = self::rootPath() . '/logs/.htaccess';
        if (!\is_file($htaccess)) {
            \file_put_contents($htaccess, "Deny from all\n", \LOCK_EX);
        }

        return $dir;
    }

    private static function sanitizeFileName(string $name): string
    {
        // Убираем всё, кроме букв, цифр, дефисов, подчёркиваний и точек
        return \preg_replace('/[^A-Za-z0-9_\-.]/', '_', $name) ?: 'data';
    }

    //endregion
}