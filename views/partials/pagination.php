<?php
// Needs: $total, $perPage
$pages   = (int) ceil($total / $perPage);
$current = max(1, (int) ($_GET['page'] ?? 1));

if ($pages <= 1) {
    return;
}

$from = max(1, $current - 2);
$to   = min($pages, $current + 2);
?>
<nav class="mt-4" aria-label="Pages">
    <ul class="pagination justify-content-center flex-wrap">
        <li class="page-item <?= $current <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(url(current_path(), query_with(['page' => $current - 1]))) ?>">&laquo;</a>
        </li>
        <?php for ($i = $from; $i <= $to; $i++): ?>
            <li class="page-item <?= $i === $current ? 'active' : '' ?>">
                <a class="page-link" href="<?= e(url(current_path(), query_with(['page' => $i]))) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $current >= $pages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(url(current_path(), query_with(['page' => $current + 1]))) ?>">&raquo;</a>
        </li>
    </ul>
</nav>
