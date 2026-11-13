<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($publisher->name()) ?></h1>
    <div class="d-flex gap-2">
        <a href="/publishers/<?= $publisher->id() ?>/edit" class="btn btn-warning">Изменить</a>
        <form method="post" action="/publishers/<?= $publisher->id() ?>/delete" class="d-inline" onsubmit="return confirm('Удалить издательство?')">
            <button type="submit" class="btn btn-danger">Удалить</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Описание:</div>
            <div class="col-sm-9">
                <?php if ($publisher->description() === null): ?>
                    <em class="text-muted">Нет описания</em>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($publisher->description())) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Книги издательства:</div>
            <div class="col-sm-9">
                <?php if ($books === []): ?>
                    <em class="text-muted">Нет книг</em>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <p>
                            <a href="/books/<?= $book->id() ?>"><?= htmlspecialchars($book->title()) ?></a>,
                            <?= $book->year() ?>
                            <?php
                            $names = [];
                            foreach ($book->authorIds() as $authorId) {
                                if (isset($authorsById[$authorId])) {
                                    $names[] = htmlspecialchars($authorsById[$authorId]->name());
                                }
                            }
                            if ($names !== []) echo ' — ' . implode(', ', $names);
                            ?>
                        </p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="/publishers" class="btn btn-secondary">← К списку издательств</a>
</div>