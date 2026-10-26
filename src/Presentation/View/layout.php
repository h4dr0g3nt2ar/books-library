<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Домашняя библиотека') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">📚 Домашняя библиотека</a>
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="/books">Книги</a></li>
            <li class="nav-item"><a class="nav-link" href="/authors">Авторы</a></li>
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
</body>
</html>