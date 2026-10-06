<?php
// Icon for each known category name (anything else gets a default icon)
$icons = [
    'Heart'      => 'bi-heart-pulse',
    'Kidney'     => 'bi-droplet-half',
    'Neuro'      => 'bi-activity',
    'Child'      => 'bi-balloon-heart',
    'Medicine'   => 'bi-capsule',
    'Dental'     => 'bi-emoji-smile',
    'Skin'       => 'bi-stars',
    'Eye'        => 'bi-eye',
    'Orthopedic' => 'bi-person-wheelchair',
];
$iconFor = function (string $name) use ($icons) {
    foreach ($icons as $word => $icon) {
        if (stripos($name, $word) !== false) {
            return $icon;
        }
    }
    return 'bi-clipboard2-pulse';
};
?>

<section class="hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="display-5 mb-3">Find the right doctor.<br>Book in under a minute.</h1>
                <p class="lead mb-0">Search specialists across Bangladesh by name, specialization, hospital or city and pick a time that suits you.</p>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="card search-card">
        <div class="card-body p-3 p-md-4">
            <form action="<?= url('doctors') ?>" method="get" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="q">Doctor name</label>
                    <input type="text" class="form-control" id="q" name="q" placeholder="e.g. Dr. Ayesha">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="category">Specialization</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All specializations</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="city">City</label>
                    <select class="form-select" id="city" name="city">
                        <option value="">All cities</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= e($city) ?>"><?= e($city) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                </div>
            </form>
        </div>
    </div>
</div>

<section class="container mt-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h4 section-title mb-0">Browse by specialization</h2>
        <a href="<?= url('doctors') ?>" class="small">All doctors <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3">
        <?php foreach ($categories as $category): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= url('doctors', ['category' => $category['id']]) ?>" class="card category-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="icon-circle"><i class="bi <?= $iconFor($category['name']) ?>"></i></span>
                        <div class="min-w-0">
                            <div class="fw-semibold small"><?= e($category['name']) ?></div>
                            <div class="text-muted small"><?= (int) $category['doctor_count'] ?> doctors</div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php if ($doctors): ?>
<section class="container mt-5">
    <h2 class="h4 section-title mb-3">Our doctors</h2>
    <div class="row g-3">
        <?php foreach ($doctors as $doctor): ?>
            <div class="col-md-6 col-lg-4"><?= partial('partials/doctor_card', ['doctor' => $doctor]) ?></div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="container mt-5">
    <h2 class="h4 section-title mb-3">How it works</h2>
    <div class="row g-3">
        <?php
        $steps = [
            ['bi-search', 'Search', 'Find a doctor by name, specialization, hospital or city.'],
            ['bi-calendar2-week', 'Pick a time', 'See the doctor\'s free slots and choose one that suits you.'],
            ['bi-check2-circle', 'Get confirmed', 'Receive an appointment number instantly. Show it at the hospital.'],
        ];
        foreach ($steps as $i => [$icon, $heading, $text]): ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="icon-circle mb-3"><i class="bi <?= $icon ?>"></i></span>
                        <h3 class="h6"><?= $i + 1 ?>. <?= e($heading) ?></h3>
                        <p class="text-muted small mb-0"><?= e($text) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
