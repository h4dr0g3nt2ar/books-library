<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($book->title()) ?></h1>
    <div class="d-flex gap-2">
        <a href="/books/<?= $book->id() ?>/edit" class="btn btn-warning">Изменить</a>
        <form action="/books/<?= $book->id() ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Удалить книгу?')">
            <button type="submit" class="btn btn-danger">Удалить</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <!-- Авторы -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted"><?= count($book->authorIds()) > 1 ? 'Авторы' : 'Автор' ?>:</div>
            <div class="col-sm-9">
                <?php
                $authorLinks = [];
                foreach ($book->authorIds() as $authorId) {
                    if (isset($authorsById[$authorId])) {
                        $authorLinks[] = '<a href="/authors/' . $authorsById[$authorId]->id() . '">'
                                . htmlspecialchars($authorsById[$authorId]->name())
                                . '</a>';
                    }
                }
                echo $authorLinks === [] ? '—' : implode(', ', $authorLinks);
                ?>
            </div>
        </div>

        <!-- Переводчики -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Переводчики:</div>
            <div class="col-sm-9">
                <?php
                $translatorLinks = [];
                foreach ($book->translatorIds() as $translatorId) {
                    if (isset($translatorsById[$translatorId])) {
                        $translatorLinks[] = '<a href="/translators/' . $translatorsById[$translatorId]->id() . '">'
                                . htmlspecialchars($translatorsById[$translatorId]->name())
                                . '</a>';
                    }
                }
                echo $translatorLinks === [] ? '—' : implode(', ', $translatorLinks);
                ?>
            </div>
        </div>

        <!-- Теги (бейджи-ссылки с тултипом) -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Теги:</div>
            <div class="col-sm-9">
                <?php if ($book->tagIds() === []): ?>
                    —
                <?php else: ?>
                    <?php foreach ($book->tagIds() as $tagId): ?>
                        <?php if (isset($tagsById[$tagId])): ?>
                            <?php $tag = $tagsById[$tagId]; ?>
                            <a href="/tags/<?= $tag->id() ?>"
                               class="badge bg-secondary me-1 text-decoration-none"
                                    <?php if ($tag->description() !== null): ?>
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="<?= htmlspecialchars($tag->description()) ?>"
                                    <?php endif; ?>
                               style="cursor: <?= $tag->description() !== null ? 'help' : 'pointer' ?>;">
                                <?= htmlspecialchars($tag->name()) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Год издания -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Год издания:</div>
            <div class="col-sm-9"><?= $book->year() ?></div>
        </div>

        <!-- Страна — БЕЗ ссылки -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Страна:</div>
            <div class="col-sm-9"><?= $country !== null ? htmlspecialchars($country->name()) : '—' ?></div>
        </div>

        <!-- Издательство -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Издательство:</div>
            <div class="col-sm-9">
                <?php if ($publisher !== null): ?>
                    <a href="/publishers/<?= $publisher->id() ?>"><?= htmlspecialchars($publisher->name()) ?></a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </div>
        </div>

        <!-- Серия -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Серия:</div>
            <div class="col-sm-9">
                <?php if ($series !== null): ?>
                    <a href="/series/<?= $series->id() ?>"><?= htmlspecialchars($series->name()) ?></a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </div>
        </div>

        <!-- Переплёт -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Переплёт:</div>
            <div class="col-sm-9"><?= $book->binding()?->label() ?? '—' ?></div>
        </div>

        <!-- Страниц -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Страниц:</div>
            <div class="col-sm-9"><?= $book->pages() ?? '—' ?></div>
        </div>

        <!-- Тираж -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Тираж:</div>
            <div class="col-sm-9"><?= $book->printRun() ?? '—' ?></div>
        </div>

        <!-- ISBN -->
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">ISBN:</div>
            <div class="col-sm-9"><?= htmlspecialchars($book->isbn() ?? '—') ?></div>
        </div>

        <!-- Описание -->
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