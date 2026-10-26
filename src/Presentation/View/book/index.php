<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/books/create" class="btn btn-success">+ Добавить книгу</a>
</div>

<?php if ($books === []): ?>
    <div class="alert alert-info">
        Пока нет книг. <a href="/books/create">Добавьте первую</a>.
    </div>
<?php else: ?>
    <p class="text-muted">Всего книг: <?= $total ?></p>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Год</th>
            <th>Авторы</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?= $book->id() ?></td>
                <td><?= htmlspecialchars($book->title()) ?></td>
                <td><?= $book->year() ?></td>
                <td>
                    <?php
                    $names = [];
                    foreach ($book->authorIds() as $authorId) {
                        if (isset($authorsById[$authorId])) {
                            $names[] = htmlspecialchars($authorsById[$authorId]->name());
                        }
                    }
                    echo $names === [] ? '—' : implode(', ', $names);
                    ?>
                </td>
                <td class="text-end">
                    <a href="/books/<?= $book->id() ?>" class="btn btn-sm btn-outline-primary">Открыть</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>