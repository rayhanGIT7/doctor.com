<div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
    <form method="get" class="d-flex flex-wrap gap-2">
        <input type="text" name="q" value="<?= e($filters['q']) ?>" class="form-control" placeholder="Doctor name" style="max-width: 200px">
        <select name="hospital" class="form-select" style="max-width: 200px">
            <option value="">All hospitals</option>
            <?php foreach ($hospitals as $hospital): ?>
                <option value="<?= $hospital['id'] ?>" <?= selected($hospital['id'], $filters['hospital']) ?>><?= e($hospital['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="category" class="form-select" style="max-width: 200px">
            <option value="">All categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= selected($category['id'], $filters['category']) ?>><?= e($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="form-select" style="max-width: 150px">
            <option value="">All status</option>
            <option value="active" <?= selected('active', $filters['status']) ?>>Active</option>
            <option value="inactive" <?= selected('inactive', $filters['status']) ?>>Inactive</option>
        </select>
        <button class="btn btn-light border">Filter</button>
    </form>
    <a href="<?= url('admin/doctors/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add doctor</a>
</div>

<div class="card">
    <?php if (!$doctors): ?>
        <div class="empty-state"><i class="bi bi-person-badge"></i> No doctors found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Doctor</th><th>Specialization</th><th>Hospital</th><th>Fee</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?= avatar($doctor['image'], $doctor['name'], 40) ?>
                                <div>
                                    <div class="fw-semibold"><?= e($doctor['name']) ?></div>
                                    <div class="small text-muted"><?= e($doctor['phone']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="small"><?= e($doctor['categories']) ?></td>
                        <td class="small"><?= e($doctor['hospital_name']) ?><div class="text-muted"><?= e($doctor['city']) ?></div></td>
                        <td><?= money($doctor['consultation_fee']) ?></td>
                        <td><?= status_badge($doctor['status']) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= url('admin/doctors/' . $doctor['id'] . '/edit') ?>" class="btn btn-sm btn-light border" title="Edit"><i class="bi bi-pencil"></i></a>
                            <a href="<?= url('admin/schedules', ['doctor_id' => $doctor['id']]) ?>" class="btn btn-sm btn-light border" title="Schedule"><i class="bi bi-clock"></i></a>
                            <form method="post" action="<?= url('admin/doctors/' . $doctor['id'] . '/status') ?>" class="d-inline"
                                  data-confirm="<?= $doctor['status'] === 'active' ? 'Deactivate this doctor? They will be hidden from search and cannot log in.' : 'Activate this doctor?' ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light border" title="<?= $doctor['status'] === 'active' ? 'Deactivate' : 'Activate' ?>">
                                    <i class="bi <?= $doctor['status'] === 'active' ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= partial('partials/pagination', ['total' => $total, 'perPage' => $perPage]) ?>
