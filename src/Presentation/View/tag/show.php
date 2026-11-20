<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($tag->name()) ?></h1>
    <div class="d-flex gap-2">
        <a href="/tags/<?= $tag->id() ?>/edit" class="btn btn-warning">Изменить</a>
        <form action="/tags/<?= $tag->id() ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Удалить переводчика?')">
            <button type="submit" class="btn btn-danger">Удалить</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Описание:</div>
            <div class="col-sm-9">
                <?php if ($tag->description() === null): ?>
                    <em class="text-muted">Нет описания</em>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($tag->description())) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-3 text-muted">Книги,с этим тегом:</div>
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
                            $links = [];
                            foreach ($book->authorIds() as $authorId) {
                                if (isset($authorsById[$authorId])) {
                                    $names[$authorId] = htmlspecialchars($authorsById[$authorId]->name());
                                    $links[$authorId] = "/authors/" . $authorsById[$authorId]->id();
                                }
                            }
                            $lastKey = array_key_last($names);
                            if ($names !== []) echo ' — ' ;
                            foreach ($names as $authorId => $name) {
                                ?><a href="<?=$links[$authorId]?>"><?=htmlspecialchars($name);?></a><?
                                if($authorId !== $lastKey){
                                    echo ', ';
                                }
                            }

                            ?>
                        </p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="/tags" class="btn btn-secondary">← К списку тегов</a>
</div>