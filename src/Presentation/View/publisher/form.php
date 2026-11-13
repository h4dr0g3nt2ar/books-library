<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/publishers" class="btn btn-secondary">← К списку</a>
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

<form method="post" action="<?= $publisher === null ? '/publishers' : '/publishers/' . $publisher->id() ?>" class="col-md-6">
    <div class="mb-3">
        <label for="name" class="form-label">Название издательства</label>
        <input type="text" name="name" id="name" class="form-control" required
               value="<?= $publisher !== null ? htmlspecialchars($publisher->name()) : htmlspecialchars($old['name'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Описание</label>
        <textarea name="description" id="description" class="form-control" rows="4"><?= $publisher !== null ? htmlspecialchars($publisher->description() ?? '') : htmlspecialchars($old['description'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
    <a href="/publishers" class="btn btn-outline-secondary">Отмена</a>
</form>