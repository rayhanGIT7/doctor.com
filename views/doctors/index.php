<div class="container py-4">
    <div class="row g-4">
        <!-- Filters -->
        <aside class="col-lg-3">
            <form method="get" action="<?= url('doctors') ?>" class="card">
                <div class="card-body">
                    <h2 class="h6 mb-3"><i class="bi bi-funnel"></i> Filter doctors</h2>

                    <div class="mb-3">
                        <label class="form-label" for="q">Doctor name</label>
                        <input type="text" class="form-control" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Search by name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="category">Specialization</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= selected($category['id'], $filters['category']) ?>><?= e($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="hospital">Hospital</label>
                        <select class="form-select" id="hospital" name="hospital">
                            <option value="">All</option>
                            <?php foreach ($hospitals as $hospital): ?>
                                <option value="<?= $hospital['id'] ?>" <?= selected($hospital['id'], $filters['hospital']) ?>><?= e($hospital['name']) ?> (<?= e($hospital['city']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="city">City</label>
                        <select class="form-select" id="city" name="city">
                            <option value="">All</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?= e($city) ?>" <?= selected($city, $filters['city']) ?>><?= e($city) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="date">Available on</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?= e($filters['date']) ?>" min="<?= today() ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="gender">Doctor gender</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">Any</option>
                            <option value="male" <?= selected('male', $filters['gender']) ?>>Male</option>
                            <option value="female" <?= selected('female', $filters['gender']) ?>>Female</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary">Apply filters</button>
                        <a href="<?= url('doctors') ?>" class="btn btn-light">Reset</a>
                    </div>
                </div>
            </form>
        </aside>

        <!-- Results -->
        <section class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 section-title mb-0">Doctors</h1>
                <span class="text-muted small"><?= (int) $total ?> found</span>
            </div>

            <?php if (!$doctors): ?>
                <div class="card">
                    <div class="empty-state">
                        <i class="bi bi-person-x"></i>
                        No doctors match your search. Try removing some filters.
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($doctors as $doctor): ?>
                        <div class="col-md-6 col-xl-4"><?= partial('partials/doctor_card', ['doctor' => $doctor]) ?></div>
                    <?php endforeach; ?>
                </div>
                <?= partial('partials/pagination', ['total' => $total, 'perPage' => $perPage]) ?>
            <?php endif; ?>
        </section>
    </div>
</div>
