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
    <div class="row mb-3">
        <div class="col-md-8">
            <label for="title" class="form-label">Название</label>
            <input type="text" name="title" id="title" class="form-control" required
                   value="<?= $book !== null ? htmlspecialchars($book->title()) : htmlspecialchars($old['title'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label for="year" class="form-label">Год издания</label>
            <input type="number" name="year" id="year" class="form-control" required
                   value="<?= $book !== null ? $book->year() : htmlspecialchars($old['year'] ?? '') ?>">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="binding" class="form-label">Переплёт</label>
            <select name="binding" id="binding" class="form-select">
                <option value="">— Не указан —</option>
                <option value="hardcover" <?= ($book !== null && $book->binding()?->value === 'hardcover') || (isset($old['binding']) && $old['binding'] === 'hardcover') ? 'selected' : '' ?>>Твёрдый</option>
                <option value="paperback" <?= ($book !== null && $book->binding()?->value === 'paperback') || (isset($old['binding']) && $old['binding'] === 'paperback') ? 'selected' : '' ?>>Мягкий</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" name="isbn" id="isbn" class="form-control" placeholder="978-..."
                   value="<?= $book !== null ? htmlspecialchars($book->isbn() ?? '') : htmlspecialchars($old['isbn'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label for="pages" class="form-label">Страниц</label>
            <input type="number" name="pages" id="pages" class="form-control" placeholder="350"
                   value="<?= $book !== null ? ($book->pages() ?? '') : htmlspecialchars($old['pages'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label for="print_run" class="form-label">Тираж</label>
            <input type="number" name="print_run" id="print_run" class="form-control" placeholder="5000"
                   value="<?= $book !== null ? ($book->printRun() ?? '') : htmlspecialchars($old['print_run'] ?? '') ?>">
        </div>
    </div>

    <!-- Страна, Издательство, Серия -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="country_id" class="form-label">Страна</label>
            <select name="country_id" id="country_id" class="form-select">
                <option value="">— Не указана —</option>
                <?php foreach ($countries as $country): ?>
                    <?php
                    $isSelected = ($book !== null && $book->countryId() === $country->id())
                        || (isset($old['country_id']) && (int) $old['country_id'] === $country->id());
                    ?>
                    <option value="<?= $country->id() ?>" <?= $isSelected ? 'selected' : '' ?>>
                        <?= htmlspecialchars($country->name()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="publisher_id" class="form-label">Издательство</label>
            <select name="publisher_id" id="publisher_id" class="form-select">
                <option value="">— Не указано —</option>
                <?php foreach ($publishers as $publisher): ?>
                    <?php
                    $isSelected = ($book !== null && $book->publisherId() === $publisher->id())
                        || (isset($old['publisher_id']) && (int) $old['publisher_id'] === $publisher->id());
                    ?>
                    <option value="<?= $publisher->id() ?>" <?= $isSelected ? 'selected' : '' ?>>
                        <?= htmlspecialchars($publisher->name()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="series_id" class="form-label">Серия</label>
            <select name="series_id" id="series_id" class="form-select">
                <option value="">— Не указана —</option>
                <?php foreach ($seriesList as $seriesItem): ?>
                    <?php
                    $isSelected = ($book !== null && $book->seriesId() === $seriesItem->id())
                        || (isset($old['series_id']) && (int) $old['series_id'] === $seriesItem->id());
                    ?>
                    <option value="<?= $seriesItem->id() ?>" <?= $isSelected ? 'selected' : '' ?>>
                        <?= htmlspecialchars($seriesItem->name()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Описание</label>
        <textarea name="description" id="description" class="form-control" rows="4"><?= $book !== null ? htmlspecialchars($book->description() ?? '') : htmlspecialchars($old['description'] ?? '') ?></textarea>
    </div>

    <!-- Авторы -->
    <div class="mb-3">
        <label for="authors" class="form-label">Авторы</label>
        <select name="authorIds[]" id="authors" class="form-select" multiple>
            <?php foreach ($authors as $author): ?>
                <?php
                $isSelected = false;
                if ($book !== null) {
                    $isSelected = in_array($author->id(), $book->authorIds(), true);
                } elseif (isset($old['authorIds'])) {
                    $isSelected = in_array($author->id(), array_map('intval', $old['authorIds']), true);
                }
                ?>
                <option value="<?= $author->id() ?>" <?= $isSelected ? 'selected' : '' ?>>
                    <?= htmlspecialchars($author->name()) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Удерживайте Ctrl (Cmd), чтобы выбрать нескольких авторов.</div>
    </div>

    <!-- Переводчики -->
    <div class="mb-3">
        <label for="translators" class="form-label">Переводчики</label>
        <select name="translatorIds[]" id="translators" class="form-select" multiple>
            <?php foreach ($translators as $translator): ?>
                <?php
                $isSelected = false;
                if ($book !== null) {
                    $isSelected = in_array($translator->id(), $book->translatorIds(), true);
                } elseif (isset($old['translatorIds'])) {
                    $isSelected = in_array($translator->id(), array_map('intval', $old['translatorIds']), true);
                }
                ?>
                <option value="<?= $translator->id() ?>" <?= $isSelected ? 'selected' : '' ?>>
                    <?= htmlspecialchars($translator->name()) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Удерживайте Ctrl (Cmd), чтобы выбрать нескольких переводчиков.</div>
    </div>

    <div class="mb-3">
        <label for="tags" class="form-label">Теги</label>
        <select name="tagIds[]" id="tags" class="form-select" multiple>
            <?php foreach ($tags as $tag): ?>
                <?php
                $isSelected = false;
                if ($book !== null) {
                    $isSelected = in_array($tag->id(), $book->tagIds(), true);
                } elseif (isset($old['tagIds'])) {
                    $isSelected = in_array($tag->id(), array_map('intval', $old['tagIds']), true);
                }
                ?>
                <option value="<?= $tag->id() ?>" <?= $isSelected ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tag->name()) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Удерживайте Ctrl (Cmd) для выбора нескольких.</div>
    </div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
    <a href="/books" class="btn btn-outline-secondary">Отмена</a>
</form>