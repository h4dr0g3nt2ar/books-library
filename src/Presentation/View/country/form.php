<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/countries" class="btn btn-secondary">← К списку</a>
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

<form method="post" action="<?= $country === null ? '/countries' : '/countries/' . $country->id() ?>" class="col-md-6">
    <div class="mb-3">
        <label for="name" class="form-label">Название страны</label>
        <input type="text" name="name" id="name" class="form-control" required
               value="<?= $country !== null ? htmlspecialchars($country->name()) : htmlspecialchars($old['name'] ?? '') ?>">
    </div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
    <a href="/countries" class="btn btn-outline-secondary">Отмена</a>
</form>