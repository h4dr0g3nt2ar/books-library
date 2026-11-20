<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Домашняя библиотека') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>
<body>

<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Функция проверки: путь начинается с нужного префикса?
$isActive = function (string $path, string $prefix): bool {
    if ($prefix === '/') {
        return $path === '/';
    }
    return $path === $prefix || \str_starts_with($path, $prefix . '/');
};

$navItems = [
        ['href' => '/books',       'label' => 'Книги',          'prefix' => '/books'],
        ['href' => '/authors',     'label' => 'Авторы',         'prefix' => '/authors'],
        ['href' => '/publishers',  'label' => 'Издательства',   'prefix' => '/publishers'],
        ['href' => '/series',      'label' => 'Книжные серии',  'prefix' => '/series'],
        ['href' => '/translators', 'label' => 'Переводчики',    'prefix' => '/translators'],
        ['href' => '/tags',        'label' => 'Теги',           'prefix' => '/tags'],
        ['href' => '/countries',   'label' => 'Страны',         'prefix' => '/countries'],
];
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">📚 Домашняя библиотека</a>
        <ul class="navbar-nav">
            <?php foreach ($navItems as $item): ?>
                <?php $active = $isActive($currentPath, $item['prefix']); ?>
                <li class="nav-item">
                    <a class="nav-link <?= $active ? 'active' : '' ?>"
                       href="<?= htmlspecialchars($item['href']) ?>"
                            <?= $active ? 'aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<main class="container my-4">
    <?= $content ?? '' ?>
</main>

<footer class="text-center text-muted py-4">
    <small>Домашняя библиотека · <?= date('Y') ?></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function() {
        $('#authors').select2({
            theme: 'bootstrap-5',
            placeholder: 'Начните вводить имя автора...',
            allowClear: true,
            width: '100%',
            language: { noResults: function() { return "Ничего не найдено"; } }
        });

        $('#translators').select2({
            theme: 'bootstrap-5',
            placeholder: 'Начните вводить имя переводчика...',
            allowClear: true,
            width: '100%',
            language: { noResults: function() { return "Ничего не найдено"; } }
        });
        $('#tags').select2({
            theme: 'bootstrap-5',
            placeholder: 'Начните вводить название тега...',
            allowClear: true,
            width: '100%',
            language: { noResults: function() { return "Ничего не найдено"; } }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>
</body>
</html>