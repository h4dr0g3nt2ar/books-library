<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/translators/create" class="btn btn-success">+ Добавить переводчика</a>
</div>

<?php if ($translators === []): ?>
    <div class="alert alert-info">Пока нет переводчиков.</div>
<?php else: ?>
    <p class="text-muted">Всего переводчиков: <?= $total ?></p>

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
        <?php foreach ($translators as $translator): ?>
            <tr>
                <td><?= $translator->id() ?></td>
                <td><a href="/translators/<?= $translator->id() ?>"><?= htmlspecialchars($translator->name()) ?></a></td>
                <td><?= $translator->createdAt()->format('d.m.Y H:i') ?></td>
                <td class="text-end">
                    <a href="/translators/<?= $translator->id() ?>" class="btn btn-sm btn-outline-primary">Открыть</a>
                    <a href="/translators/<?= $translator->id() ?>/edit" class="btn btn-sm btn-secondary">Изменить</a>
                    <form action="/translators/<?= $translator->id() ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Удалить переводчика?')">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>