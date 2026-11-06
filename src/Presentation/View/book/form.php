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
               value="<?= $book !== null ? htmlspecialchars($book->title()) : '' ?>">
    </div>

    <div class="mb-3">
        <label for="year" class="form-label">Год издания</label>
        <input type="number" name="year" id="year" class="form-control" required
               value="<?= $book !== null ? $book->year() : '' ?>">
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Описание</label>
        <textarea name="description" id="description" class="form-control" rows="4"><?= $book !== null ? htmlspecialchars($book->description() ?? '') : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label for="authors" class="form-label">Авторы</label>
        <select name="authorIds[]" id="authors" class="form-select" multiple>
            <?php foreach ($authors as $author): ?>
                <?php
                $isSelected = $book !== null && in_array($author->id(), $book->authorIds(), true);
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
