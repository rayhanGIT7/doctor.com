<!doctype html>
<html lang="en">
<head>
    <?= partial('partials/head', ['title' => $title ?? 'Error']) ?>
</head>
<body class="d-flex align-items-center min-vh-100">
    <?= $content ?>
</body>
</html>
