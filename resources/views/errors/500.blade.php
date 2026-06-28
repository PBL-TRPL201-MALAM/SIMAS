<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.favicon')
    <title>Terjadi Kesalahan - SIMAS</title>
    <style>
      :root {
        color-scheme: light;
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      }

      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        min-height: 100vh;
        background: #f8fafc;
        color: #0f172a;
        display: grid;
        place-items: center;
        padding: 24px;
      }

      .panel {
        width: min(100%, 520px);
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
      }

      .label {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 12px;
        margin-bottom: 18px;
      }

      h1 {
        margin: 0;
        font-size: 24px;
        line-height: 1.25;
      }

      p {
        margin: 12px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
      }

      .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 24px;
      }

      a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
      }

      .primary {
        background: #2563eb;
        color: #ffffff;
      }

      .secondary {
        background: #f1f5f9;
        color: #475569;
      }
    </style>
  </head>
  <body>
    <main class="panel">
      <div class="label">SIMAS 500</div>
      <h1>Terjadi kesalahan pada sistem.</h1>
      <p>Halaman belum bisa ditampilkan. Silakan kembali ke dashboard atau coba lagi beberapa saat lagi.</p>
      <div class="actions">
        <a class="primary" href="/dashboard">Ke Dashboard</a>
        <a class="secondary" href="/">Ke Beranda</a>
      </div>
    </main>
  </body>
</html>
