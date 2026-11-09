<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/authors" class="btn btn-secondary">← К списку</a>
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

<form method="post" action="<?= $author === null ? '/authors' : '/authors/' . $author->id() ?>">
    <div class="mb-3">
        <label for="title" class="form-label">Имя автора</label>
        <input type="text" name="name" id="name" class="form-control" required
               value="<?= $author !== null ? htmlspecialchars($author->name()) : '' ?>">
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Описание</label>
        <textarea name="description" id="description" class="form-control" rows="4"><?= $author !== null ? htmlspecialchars($author->description() ?? '') : '' ?></textarea>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="birth_year" class="form-label">Год рождения</label>
            <input type="number" name="birth_year" id="birth_year" class="form-control"
                   placeholder="например, 1828"
                   value="<?= $author !== null && method_exists($author, 'birthYear') ? ($author->birthYear() ?? '') : '' ?>">
        </div>
        <div class="col-md-6">
            <label for="death_year" class="form-label">Год смерти</label>
            <input type="number" name="death_year" id="death_year" class="form-control"
                   placeholder="например, 1910"
                   value="<?= $author !== null && method_exists($author, 'deathYear') ? ($author->deathYear() ?? '') : '' ?>">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Сохранить</button>
    <a href="/authors" class="btn btn-outline-secondary">Отмена</a>
</form>
