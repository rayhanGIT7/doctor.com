<form method="get" class="d-flex gap-2 mb-3">
    <input type="text" name="q" value="<?= e($search) ?>" class="form-control" placeholder="Name, email or phone" style="max-width: 280px">
    <button class="btn btn-light border">Search</button>
</form>

<div class="card">
    <?php if (!$users): ?>
        <div class="empty-state"><i class="bi bi-people"></i> No patients found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Name</th><th>Contact</th><th>Appointments</th><th>Joined</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($user['name']) ?></td>
                        <td class="small"><?= e($user['email']) ?><div class="text-muted"><?= e($user['phone']) ?></div></td>
                        <td>
                            <a href="<?= url('admin/appointments', ['q' => $user['phone']]) ?>"><?= (int) $user['appointment_count'] ?></a>
                        </td>
                        <td class="small"><?= format_date($user['created_at']) ?></td>
                        <td><?= status_badge($user['status']) ?></td>
                        <td class="text-end">
                            <form method="post" action="<?= url('admin/users/' . $user['id'] . '/status') ?>"
                                  data-confirm="<?= $user['status'] === 'active' ? 'Deactivate this account? The patient will not be able to log in.' : 'Activate this account?' ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light border"><?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?></button>
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
