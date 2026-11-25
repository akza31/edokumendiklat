<div class="page-heading"><div><p class="eyebrow">MASTER DATA</p><h1>Institusi Pendidikan</h1></div></div>
<?php if ($user['role'] === 'admin'): ?>
<section class="panel">
  <h2>Tambah institusi</h2>
  <form method="post" action="/institutions" class="form-grid">
    <?= csrf_field() ?>
    <label>Nama institusi<input name="name" maxlength="160" required></label>
    <label>Jenis<input name="institution_type" maxlength="80" value="Perguruan Tinggi"></label>
    <label>Nama kontak<input name="contact_name" maxlength="120"></label>
    <label>Email kontak<input type="email" name="contact_email" maxlength="190"></label>
    <label>Telepon<input name="phone" maxlength="40"></label>
    <div class="form-action"><button class="primary">Simpan institusi</button></div>
  </form>
</section>
<?php endif; ?>
<section class="panel">
  <h2>Daftar institusi</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Nama</th><th>Jenis</th><th>Kontak</th><th>Email</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $row): ?>
      <tr><td><?= e($row['name']) ?></td><td><?= e($row['institution_type']) ?></td><td><?= e($row['contact_name']) ?></td><td><?= e($row['contact_email']) ?></td><td><?= (int) $row['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="5" class="empty">Belum ada institusi.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</section>
