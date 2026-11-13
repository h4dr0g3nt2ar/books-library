<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/publishers/create" class="btn btn-success">+ Добавить издательство</a>
</div>

<?php if ($publishers === []): ?>
    <div class="alert alert-info">Пока нет издательств.</div>
<?php else: ?>
    <p class="text-muted">Всего издательств: <?= $total ?></p>

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
        <?php foreach ($publishers as $publisher): ?>
            <tr>
                <td><?= $publisher->id() ?></td>
                <td><?= htmlspecialchars($publisher->name()) ?></td>
                <td><?= $publisher->createdAt()->format('d.m.Y H:i') ?></td>
                <td class="text-end">
                    <a href="/publishers/<?= $publisher->id() ?>" class="btn btn-sm btn-outline-primary">Открыть</a>
                    <a href="/publishers/<?= $publisher->id() ?>/edit" class="btn btn-sm btn-secondary">Изменить</a>
                    <form action="/publishers/<?= $publisher->id() ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Удалить издательство?')">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>