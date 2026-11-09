<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/authors/create" class="btn btn-success">+ Добавить автора</a>
</div>

<?php if ($authors === []): ?>
    <div class="alert alert-info">
        Пока нет авторов. <a href="/authors/create">Добавьте первого</a>.
    </div>
<?php else: ?>
    <p class="text-muted">Всего авторов: <?= $total ?></p>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Годы жизни</th>
            <th>Дата создания</th>
            <th>Дата изменения</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($authors as $author): ?>
            <tr>
                <td><?= $author->id() ?></td>
                <td><?= htmlspecialchars($author->name()) ?></td>
                <td><?=$author->birthYear()?> - <?=$author->deathYear() !== null ? $author->deathYear() : '...'?></td>
                <td><?=$author->createdAt()->format('d.m.Y H:i:s')?></td>
                <td><?=$author->updatedAt()->format('d.m.Y H:i:s')?></td>
                <td class="text-end">
                    <a href="/authors/<?= $author->id() ?>" class="btn btn-sm btn-outline-primary">Открыть</a>
                    <a href="/authors/<?= $author->id()?>/edit" class="btn btn-sm btn-secondary">Изменить</a>
                    <form method="post" action="/authors/<?= $author->id() ?>/delete" class="d-inline" onsubmit="return confirm('Удалить автора?')">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

