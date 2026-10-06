<div class="d-flex justify-content-end mb-3">
    <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add category</a>
</div>

<div class="card">
    <?php if (!$categories): ?>
        <div class="empty-state"><i class="bi bi-tags"></i> No categories yet.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Name</th><th>Description</th><th>Doctors</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($category['name']) ?></td>
                        <td class="small text-muted"><?= e($category['description']) ?></td>
                        <td><?= (int) $category['doctor_count'] ?></td>
                        <td><?= status_badge($category['status']) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= url('admin/categories/' . $category['id'] . '/edit') ?>" class="btn btn-sm btn-light border" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= url('admin/categories/' . $category['id'] . '/status') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light border" title="<?= $category['status'] === 'active' ? 'Deactivate' : 'Activate' ?>">
                                    <i class="bi <?= $category['status'] === 'active' ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
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
