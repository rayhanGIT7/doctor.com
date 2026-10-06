<div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
    <form method="get" class="d-flex flex-wrap gap-2">
        <input type="text" name="q" value="<?= e($search) ?>" class="form-control" placeholder="Search name or city" style="max-width: 240px">
        <select name="status" class="form-select" style="max-width: 160px">
            <option value="">All status</option>
            <option value="active" <?= selected('active', $status) ?>>Active</option>
            <option value="inactive" <?= selected('inactive', $status) ?>>Inactive</option>
        </select>
        <button class="btn btn-light border">Filter</button>
    </form>
    <a href="<?= url('admin/hospitals/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add hospital</a>
</div>

<div class="card">
    <?php if (!$hospitals): ?>
        <div class="empty-state"><i class="bi bi-hospital"></i> No hospitals found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Name</th><th>City</th><th>Contact</th><th>Doctors</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($hospitals as $hospital): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($hospital['name']) ?><div class="small text-muted fw-normal"><?= e($hospital['address']) ?></div></td>
                        <td><?= e($hospital['city']) ?><div class="small text-muted"><?= e($hospital['country']) ?></div></td>
                        <td class="small"><?= e($hospital['phone']) ?><div class="text-muted"><?= e($hospital['email']) ?></div></td>
                        <td><?= (int) $hospital['doctor_count'] ?></td>
                        <td><?= status_badge($hospital['status']) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= url('admin/hospitals/' . $hospital['id'] . '/edit') ?>" class="btn btn-sm btn-light border" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= url('admin/hospitals/' . $hospital['id'] . '/status') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light border" title="<?= $hospital['status'] === 'active' ? 'Deactivate' : 'Activate' ?>">
                                    <i class="bi <?= $hospital['status'] === 'active' ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                </button>
                            </form>
                            <?php if ((int) $hospital['doctor_count'] === 0): ?>
                                <form method="post" action="<?= url('admin/hospitals/' . $hospital['id'] . '/delete') ?>" class="d-inline" data-confirm="Delete this hospital permanently?">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-light border text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= partial('partials/pagination', ['total' => $total, 'perPage' => $perPage]) ?>
