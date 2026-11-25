<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'E-Dokumen PKL') ?> · E-Dokumen PKL</title>
<link rel="stylesheet" href="/assets/app.css">
</head>
<body>
<?php if (!empty($user)): ?>
<header class="topbar">
  <a class="brand" href="/dashboard">E-Dokumen PKL</a>
  <nav><a href="/dashboard">Dashboard</a><a href="/institutions">Institusi</a></nav>
  <div class="account"><span><?= e($user['name']) ?></span><form method="post" action="/logout"><?= csrf_field() ?><button class="link-button">Keluar</button></form></div>
</header>
<?php endif; ?>
<main class="container">
<?php if ($message = flash('success')): ?><div class="notice success"><?= e($message) ?></div><?php endif; ?>
<?php if ($message = flash('error')): ?><div class="notice error"><?= e($message) ?></div><?php endif; ?>
<?php require $viewFile; ?>
</main>
<footer>RSU Islam Klaten · KOMKORDIK</footer>
</body>
</html>
