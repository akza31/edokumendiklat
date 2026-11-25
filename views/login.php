<section class="login-wrap">
  <div class="login-card">
    <p class="eyebrow">KOMKORDIK · RSU ISLAM KLATEN</p>
    <h1>Masuk E-Dokumen PKL</h1>
    <p class="muted">Portal pengelolaan praktik klinik mahasiswa.</p>
    <form method="post" action="/login" class="stack">
      <?= csrf_field() ?>
      <label>Email
        <input type="email" name="email" maxlength="190" autocomplete="username" required>
      </label>
      <label>Password
        <input type="password" name="password" maxlength="200" autocomplete="current-password" required>
      </label>
      <button type="submit" class="primary">Masuk</button>
    </form>
  </div>
</section>
