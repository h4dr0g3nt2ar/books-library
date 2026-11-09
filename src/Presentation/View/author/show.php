<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($author->name()) ?></h1>
    <div class="d-flex gap-2">
        <a href="/authors/<?= $author->id() ?>/edit" class="btn btn-primary">Редактировать</a>
        <form method="post" action="/authors/<?= $author->id() ?>/delete" class="d-inline" onsubmit="return confirm('Удалить автора?')">
            <button type="submit" class="btn btn-danger">Удалить</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <?php if ($author->birthYear() !== null || $author->deathYear() !== null): ?>
            <div class="row mb-3">
                <div class="col-sm-3 text-muted">Годы жизни:</div>
                <div class="col-sm-9">
                    <?php if ($author->birthYear() !== null): ?>
                        <?= $author->birthYear() ?>
                    <?php endif; ?>
                    <?php if ($author->deathYear() !== null): ?>
                        — <?= $author->deathYear() ?>
                    <?php elseif ($author->birthYear() !== null): ?>
                        — н.в.
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Описание:</div>
            <div class="col-sm-9">
                <?php if ($author->description() === null): ?>
                    <em class="text-muted">Нет описания</em>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($author->description())) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Книги автора:</div>
            <div class="col-sm-9">
                <?php if ($booksByAuthor === []): ?>
                    <em class="text-muted">Нет книг</em>
                <?php else: ?>
                    <?php foreach ($booksByAuthor as $book): ?>
                        <p>
                            <a href="/books/<?= $book->id() ?>"><?= htmlspecialchars($book->title()) ?></a>,
                            <?= $book->year() ?>
                        </p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <div class="row text-muted small">
            <div class="col-sm-6">
                Добавлено: <?= $author->createdAt()->format('d.m.Y H:i') ?>
            </div>
            <div class="col-sm-6 text-sm-end">
                Обновлено: <?= $author->updatedAt()->format('d.m.Y H:i') ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="/authors" class="btn btn-secondary">← К списку авторов</a>
</div>