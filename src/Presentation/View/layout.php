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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function() {
        $('#authors').select2({
            theme: 'bootstrap-5',
            placeholder: 'Начните вводить имя автора...',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Ничего не найдено";
                }
            }
        });
    });
</script>

</body>
</html>