<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/series/create" class="btn btn-success">+ Добавить серию</a>
</div>

<?php if ($series === []): ?>
    <div class="alert alert-info">Пока нет серий.</div>
<?php else: ?>
    <p class="text-muted">Всего серий: <?= $total ?></p>

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
        <?php foreach ($series as $series): ?>
            <tr>
                <td><?= $series->id() ?></td>
                <td><?= htmlspecialchars($series->name()) ?></td>
                <td><?= $series->createdAt()->format('d.m.Y H:i') ?></td>
                <td class="text-end">
                    <a href="/series/<?= $series->id() ?>" class="btn btn-sm btn-outline-primary">Открыть</a>
                    <a href="/series/<?= $series->id() ?>/edit" class="btn btn-sm btn-secondary">Изменить</a>
                    <form action="/series/<?= $series->id() ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Удалить серию?')">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>