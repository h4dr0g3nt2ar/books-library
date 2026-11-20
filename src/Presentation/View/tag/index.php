<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/tags/create" class="btn btn-success">+ Добавить тег</a>
</div>

<?php if ($tags === []): ?>
    <div class="alert alert-info">Пока нет тегов.</div>
<?php else: ?>
    <p class="text-muted">Всего тегов: <?= $total ?></p>

    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Добавлено</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tags as $tag): ?>
            <tr>
                <td><?= $tag->id() ?></td>
                <td><a href="/tags/<?= $tag->id() ?>"><?= htmlspecialchars($tag->name()) ?></a></td>
                <td><?= $tag->createdAt()->format('d.m.Y H:i') ?></td>
                <td class="text-end">
                    <a href="/tags/<?= $tag->id() ?>" class="btn btn-sm btn-outline-primary">Открыть</a>
                    <a href="/tags/<?= $tag->id() ?>/edit" class="btn btn-sm btn-secondary">Изменить</a>
                    <form action="/tags/<?= $tag->id() ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Удалить тег?')">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>