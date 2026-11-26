
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/books/create" class="btn btn-success">+ Добавить книгу</a>
</div>

<!-- Форма фильтров -->
<div class="card mb-4">
    <div class="card-body">

        <?php if ($activeFilters !== []): ?>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="text-muted">Фильтры:</span>
                <?php foreach ($activeFilters as $filter): ?>
                    <a href="<?= htmlspecialchars(buildFilterUrl($filters, [$filter['key'] => null])) ?>"
                       class="badge bg-primary text-decoration-none">
                        <?= htmlspecialchars($filter['label']) ?> ✕
                    </a>
                <?php endforeach; ?>
                <a href="/books" class="btn btn-sm btn-link">Сбросить всё</a>
            </div>
        <?php endif; ?>

        <form method="get" action="/books" class="row g-2">
            <div class="col-md-4">
                <label class="form-label small text-muted">Название</label>
                <input type="text" name="title" class="form-control<?= $isActive('title') ? ' border-success' : '' ?>"
                       value="<?= htmlspecialchars($filters['title']) ?>"
                       placeholder="Поиск по названию...">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Автор</label>
                <select name="author_id" class="form-select js-select2<?= $isActive('author_id') ? ' border-success' : '' ?>">
                    <option value="">Все авторы</option>
                    <?php foreach ($allAuthors as $author): ?>
                        <option value="<?= $author->id() ?>" <?= $filters['author_id'] === $author->id() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($author->name()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Издательство</label>
                <select name="publisher_id" class="form-select js-select2<?= $isActive('publisher_id') ? ' border-success' : '' ?>">
                    <option value="">Все издательства</option>
                    <?php foreach ($allPublishers as $publisher): ?>
                        <option value="<?= $publisher->id() ?>" <?= $filters['publisher_id'] === $publisher->id() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($publisher->name()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Серия</label>
                <select name="series_id" class="form-select js-select2<?= $isActive('series_id') ? ' border-success' : '' ?>">
                    <option value="">Все серии</option>
                    <?php foreach ($allSeries as $seriesItem): ?>
                        <option value="<?= $seriesItem->id() ?>" <?= $filters['series_id'] === $seriesItem->id() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($seriesItem->name()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Тег</label>
                <select name="tag_id" class="form-select js-select2<?= $isActive('tag_id') ? ' border-success' : '' ?>">
                    <option value="">Все теги</option>
                    <?php foreach ($allTags as $tag): ?>
                        <option value="<?= $tag->id() ?>" <?= $filters['tag_id'] === $tag->id() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tag->name()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Страна</label>
                <select name="country_id" class="form-select<?= $isActive('country_id') ? ' border-success' : '' ?>">
                    <option value="">Все страны</option>
                    <?php foreach ($allCountries as $country): ?>
                        <option value="<?= $country->id() ?>" <?= $filters['country_id'] === $country->id() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country->name()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Год от</label>
                <input type="number" name="year_from" class="form-control<?= $isActive('year_from') ? ' border-success' : '' ?>" min="1000" max="2100"
                       value="<?= $filters['year_from'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Год до</label>
                <input type="number" name="year_to" class="form-control<?= $isActive('year_to') ? ' border-success' : '' ?>" min="1000" max="2100"
                       value="<?= $filters['year_to'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Переплёт</label>
                <select name="binding" class="form-select<?= $isActive('binding') ? ' border-success' : '' ?>">
                    <option value="">Все</option>
                    <option value="hardcover" <?= $filters['binding'] === 'hardcover' ? 'selected' : '' ?>>Твёрдый</option>
                    <option value="paperback" <?= $filters['binding'] === 'paperback' ? 'selected' : '' ?>>Мягкий</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Найти</button>
                <a href="/books" class="btn btn-outline-secondary">Сбросить</a>
            </div>
        </form>
    </div>
</div>

<!-- Результаты -->
<?php if ($books === []): ?>
    <div class="alert alert-info">
        По вашему запросу ничего не найдено. <a href="/books">Сбросить фильтры</a>.
    </div>
<?php else: ?>
    <p class="text-muted">Найдено книг: <?= $total ?></p>

    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Год</th>
            <th>Авторы</th>
            <th>Дата создания</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?= $book->id() ?></td>
                <td><a href="/books/<?= $book->id() ?>"><?= htmlspecialchars($book->title()) ?></a></td>
                <td><?= $book->year() ?></td>
                <td>
                    <?php
                    $names = [];
                    foreach ($book->authorIds() as $authorId) {
                        if (isset($authorsById[$authorId])) {
                            $names[] = htmlspecialchars($authorsById[$authorId]->name());
                        }
                    }
                    echo $names === [] ? '—' : implode(', ', $names);
                    ?>
                </td>
                <td><?= $book->createdAt()->format('d.m.Y H:i') ?></td>
                <td class="text-end">
                    <a href="/books/<?= $book->id() ?>" class="btn btn-sm btn-outline-primary">Открыть</a>
                    <a href="/books/<?= $book->id() ?>/edit" class="btn btn-sm btn-secondary">Изменить</a>
                    <form action="/books/<?= $book->id() ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Удалить книгу?')">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Пагинация книг">
            <ul class="pagination justify-content-center">
                <!-- Назад -->
                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <?php if ($currentPage > 1): ?>
                        <a class="page-link" href="<?= htmlspecialchars($buildPageUrl($currentPage - 1)) ?>">←</a>
                    <?php else: ?>
                        <span class="page-link">←</span>
                    <?php endif; ?>
                </li>

                <!-- Номера -->
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p === 1 || $p === $totalPages || abs($p - $currentPage) <= 2): ?>
                        <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= htmlspecialchars($buildPageUrl($p)) ?>"><?= $p ?></a>
                        </li>
                    <?php elseif (abs($p - $currentPage) === 3): ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Вперёд -->
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <?php if ($currentPage < $totalPages): ?>
                        <a class="page-link" href="<?= htmlspecialchars($buildPageUrl($currentPage + 1)) ?>">→</a>
                    <?php else: ?>
                        <span class="page-link">→</span>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>