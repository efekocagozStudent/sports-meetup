<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 Forbidden — SportsMeet</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="text-center">
        <div class="display-1 mb-2">🚫</div>
        <h1 class="display-5 fw-bold">403</h1>
        <p class="lead text-muted">You don't have permission to view this page.</p>
        <a href="<?= url('/events') ?>" class="btn btn-primary mt-3">Browse Events</a>
    </div>
</div>
</body>
</html>
