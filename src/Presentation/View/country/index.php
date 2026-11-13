<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/countries/create" class="btn btn-success">+ Добавить страну</a>
</div>

<?php if ($countries === []): ?>
    <div class="alert alert-info">Пока нет стран.</div>
<?php else: ?>
    <p class="text-muted">Всего стран: <?= $total ?></p>

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
        <?php foreach ($countries as $country): ?>
            <tr>
                <td><?= $country->id() ?></td>
                <td><?= htmlspecialchars($country->name()) ?></td>
                <td><?= $country->createdAt()->format('d.m.Y H:i') ?></td>
                <td class="text-end">
                    <a href="/countries/<?= $country->id() ?>/edit" class="btn btn-sm btn-secondary">Изменить</a>
                    <form action="/countries/<?= $country->id() ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Удалить страну?')">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>