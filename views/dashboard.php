<div class="page-heading">
  <div><p class="eyebrow">RINGKASAN</p><h1>Dashboard</h1><p class="muted">Selamat datang, <?= e($user['name']) ?>.</p></div>
  <span class="role"><?= e(strtoupper($user['role'])) ?></span>
</div>
<div class="stats">
  <article class="stat"><span>Pengguna</span><strong><?= number_format($stats['users']) ?></strong></article>
  <article class="stat"><span>Institusi</span><strong><?= number_format($stats['institutions']) ?></strong></article>
  <article class="stat"><span>Program studi</span><strong><?= number_format($stats['programs']) ?></strong></article>
  <article class="stat"><span>Penempatan</span><strong><?= number_format($stats['placements']) ?></strong></article>
</div>
<section class="panel">
  <h2>Master data</h2>
  <p>Kelola institusi pendidikan sebagai data awal penerimaan mahasiswa praktik.</p>
  <a class="button" href="/institutions">Buka data institusi</a>
</section>
