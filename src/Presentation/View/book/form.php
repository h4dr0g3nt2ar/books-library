<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/books" class="btn btn-secondary">← К списку</a>
</div>

<?php if ($errors !== []): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= $book === null ? '/books' : '/books/' . $book->id() ?>">
    <div class="mb-3">
        <label for="title" class="form-label">Название</label>
        <input type="text" name="title" id="title" class="form-control" required
               value="<?= $book !== null ? htmlspecialchars($book->title()) : htmlspecialchars($old['title'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label for="year" class="form-label">Год издания</label>
        <input type="number" name="year" id="year" class="form-control" required
               value="<?= $book !== null ? $book->year() : htmlspecialchars($old['year'] ?? '') ?>">
    </div>

    <!-- НОВЫЕ ПОЛЯ -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="binding" class="form-label">Переплёт</label>
            <select name="binding" id="binding" class="form-select">
                <option value="">— Не указан —</option>
                <option value="hardcover" <?= ($book !== null && $book->binding()?->value === 'hardcover') || (isset($old['binding']) && $old['binding'] === 'hardcover') ? 'selected' : '' ?>>Твёрдый</option>
                <option value="paperback" <?= ($book !== null && $book->binding()?->value === 'paperback') || (isset($old['binding']) && $old['binding'] === 'paperback') ? 'selected' : '' ?>>Мягкий</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" name="isbn" id="isbn" class="form-control" placeholder="978-..."
                   value="<?= $book !== null ? htmlspecialchars($book->isbn() ?? '') : htmlspecialchars($old['isbn'] ?? '') ?>">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="pages" class="form-label">Кол-во страниц</label>
            <input type="number" name="pages" id="pages" class="form-control" placeholder="например, 350"
                   value="<?= $book !== null ? ($book->pages() ?? '') : htmlspecialchars($old['pages'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label for="print_run" class="form-label">Тираж</label>
            <input type="number" name="print_run" id="print_run" class="form-control" placeholder="например, 5000"
                   value="<?= $book !== null ? ($book->printRun() ?? '') : htmlspecialchars($old['print_run'] ?? '') ?>">
        </div>
    </div>
    <!-- КОНЕЦ НОВЫХ ПОЛЕЙ -->

    <div class="mb-3">
        <label for="description" class="form-label">Описание</label>
        <textarea name="description" id="description" class="form-control" rows="4"><?= $book !== null ? htmlspecialchars($book->description() ?? '') : htmlspecialchars($old['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label for="authors" class="form-label">Авторы</label>
        <select name="authorIds[]" id="authors" class="form-select" multiple>
            <?php foreach ($authors as $author): ?>
                <?php
                $isSelected = $book !== null
                        ? in_array($author->id(), $book->authorIds(), true)
                        : in_array($author->id(), array_map('intval', $old['authorIds'] ?? []), true);
                ?>
                <option value="<?= $author->id() ?>" <?= $isSelected ? 'selected' : '' ?>>
                    <?= htmlspecialchars($author->name()) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
    <a href="/books" class="btn btn-outline-secondary">Отмена</a>
</form>