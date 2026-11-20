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
            <th>Автор</th>
            <th>Дата создания</th>
            <th>Дата изменения</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?= $book->id() ?></td>
                <td><a href="/books/<?= $book->id() ?>"><?= htmlspecialchars($book->title()) ?></a></td>
                <td><?= $book->year() ?></td>
                <td>
                    <?
                    $names = [];
                    $links = [];
                    foreach ($book->authorIds() as $authorId) {
                        if (isset($authorsById[$authorId])) {
                            $names[$authorId] = htmlspecialchars($authorsById[$authorId]->name());
                            $links[$authorId] = "/authors/" . $authorsById[$authorId]->id();
                        }
                    }
                    $lastKey = array_key_last($names);
                    foreach ($names as $authorId => $name) {
                        ?><a href="<?=$links[$authorId]?>"><?=htmlspecialchars($name);?></a><?
                        if($authorId !== $lastKey){
                            echo ', ';
                        }
                    }
                    ?>

                </td>
                <td><?=$book->createdAt()->format('d.m.Y H:i:s')?></td>
                <td><?=$book->updatedAt()->format('d.m.Y H:i:s')?></td>
                <td class="text-end">
                    <a href="/books/<?= $book->id() ?>" class="btn btn-sm btn-outline-primary">Открыть</a>
                    <a href="/books/<?= $book->id()?>/edit" class="btn btn-sm btn-secondary">Изменить</a>
                    <form method="post" action="/books/<?= $book->id() ?>/delete" class="d-inline" onsubmit="return confirm('Удалить книгу?')">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

