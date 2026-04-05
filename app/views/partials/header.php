<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'SportsMeet') ?> &ndash; SportsMeet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
</head>
<body>

<!-- Skip to main content — visible on first Tab press, for keyboard users -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<nav class="navbar navbar-expand-lg sticky-top" aria-label="Main navigation">
    <div class="container">
        <a class="navbar-brand" href="<?= url('/') ?>">
            <!-- Sports balls icon by Freepik – flaticon.com -->
            <img src="https://cdn-icons-png.flaticon.com/512/5564/5564944.png" alt="SportsMeet logo" width="32" height="32" style="filter:brightness(0) invert(1);vertical-align:middle;margin-right:6px;">Sports<span>Meet</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <!-- Centre links -->
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/events') ?>">Explore</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/explore') ?>">Live Explorer</a>
                </li>
                <?php if (!empty($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/events/create') ?>">Create Event</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/dashboard') ?>">Dashboard</a>
                </li>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/admin') ?>">&#x1F6E1; Admin</a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
            <!-- Right side -->
            <ul class="navbar-nav align-items-lg-center gap-2">
                <?php if (!empty($_SESSION['user_id'])): ?>
                <?php
                    $__notif  = new NotificationService();
                    $__unread = $__notif->countUnread((int) $_SESSION['user_id']);
                ?>
                <li class="nav-item">
                    <a class="nav-link px-2"
                       href="<?= url('/notifications') ?>"
                       aria-label="Notifications<?= $__unread > 0 ? ', ' . (int)$__unread . ' unread' : '' ?>">
                        <span class="notif-wrap" aria-hidden="true">
                         
                            <img src="https://cdn-icons-png.flaticon.com/512/891/891012.png" alt="Notifications" width="22" height="22" style="filter:brightness(0) invert(1);">
                            <?php if ($__unread > 0): ?>
                            <span class="notif-dot"><?= (int)$__unread > 9 ? '9+' : (int)$__unread ?></span>
                            <?php endif; ?>
                        </span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 pe-0" href="#"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false"
                       aria-label="User menu for <?= e($_SESSION['username']) ?>">
                        <span class="avatar-circle"><?= e(strtoupper(substr($_SESSION['username'], 0, 1))) ?></span>
                        <span class="d-none d-lg-inline" style="color:rgba(255,255,255,.75);font-size:.875rem;">
                            <?= e($_SESSION['username']) ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end mt-2">
                        <li><a class="dropdown-item" href="<?= url('/dashboard') ?>">&#x1F4CA; My Dashboard</a></li>
                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <li><a class="dropdown-item" href="<?= url('/admin') ?>">&#x1F6E1; Admin Panel</a></li>
                        <?php endif; ?>
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center"
                               href="<?= url('/notifications') ?>">
                                <img src="https://cdn-icons-png.flaticon.com/512/891/891012.png" alt="" width="16" height="16" class="me-1"> Notifications
                                <?php if ($__unread > 0): ?>
                                <span class="badge rounded-pill ms-2" style="background:var(--accent);"><?= (int)$__unread ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item text-danger" href="<?= url('/logout') ?>">Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/login') ?>">Sign In</a>
                </li>
                <li class="nav-item ms-1">
                    <a class="btn btn-nav-accent" href="<?= url('/register') ?>">Get Started</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4" id="main-content">
