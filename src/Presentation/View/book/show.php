<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($book->title()) ?></h1>
    <a href="/books/<?= $book->id() ?>/edit" class="btn btn-primary">Редактировать</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Авторы:</div>
            <div class="col-sm-9">
                <?php
                $names = [];
                foreach ($book->authorIds() as $authorId) {
                    if (isset($authorsById[$authorId])) {
                        $names[] = htmlspecialchars($authorsById[$authorId]->name());
                    }
                }
                echo $names === [] ? '—' : implode(', ', $names);
                ?>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Год издания:</div>
            <div class="col-sm-9"><?= $book->year() ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Описание:</div>
            <div class="col-sm-9">
                <?php if ($book->description() === null): ?>
                    <em class="text-muted">Нет описания</em>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($book->description())) ?>
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <div class="row text-muted small">
            <div class="col-sm-6">
                Добавлено: <?= $book->createdAt()->format('d.m.Y H:i') ?>
            </div>
            <div class="col-sm-6 text-sm-end">
                Обновлено: <?= $book->updatedAt()->format('d.m.Y H:i') ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="/books" class="btn btn-secondary">← К списку книг</a>
</div>