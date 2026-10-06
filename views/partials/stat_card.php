<div class="card stat-card h-100">
    <div class="card-body d-flex align-items-center gap-3">
        <span class="icon-circle"><i class="bi <?= $icon ?>"></i></span>
        <div>
            <div class="stat-value"><?= (int) $value ?></div>
            <div class="stat-label"><?= e($label) ?></div>
        </div>
    </div>
</div>
