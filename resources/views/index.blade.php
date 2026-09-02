<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aceh Data Warehouse — Provinsi Aceh</title>
  {{-- Simpan style.css di: public/assets/css/style.css --}}
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  <div class="landing">
    <aside class="sidebar">
      <a href="{{ url('/') }}" class="brand">
        <div class="brand-mark">A</div>
        <div><div class="brand-title">Aceh Data<br>Warehouse</div><div class="brand-sub">Provinsi Aceh</div></div>
      </a>

      @php
        // Definisikan menu + submenu di sini. Ganti 'url' dengan route() kalau
        // route-nya sudah didefinisikan, mis. route('penduduk.index').
        $menus = [
          [
            'label' => 'Penduduk',
            'url'   => url('penduduk.index'),
            'icon'  => 'penduduk',
            'submenu' => [
              ['label' => 'Jumlah Penduduk', 'url' => url('penduduk/jumlah')],
              ['label' => 'Kepadatan Penduduk', 'url' => url('penduduk/kepadatan')],
              ['label' => 'Pertumbuhan Penduduk', 'url' => url('penduduk/pertumbuhan')],
            ],
          ],
          [
            'label' => 'Sosial',
            'url'   => '#sektor',
            'icon'  => 'sosial',
            'submenu' => [
              ['label' => 'Kemiskinan', 'url' => url('sosial/kemiskinan')],
              ['label' => 'Ketenagakerjaan', 'url' => url('sosial/ketenagakerjaan')],
              ['label' => 'Bantuan Sosial', 'url' => url('sosial/bantuan-sosial')],
            ],
          ],
          [
            'label' => 'Kesehatan',
            'url'   => '#sektor',
            'icon'  => 'kesehatan',
            'submenu' => [
              ['label' => 'Fasilitas Kesehatan', 'url' => url('kesehatan/fasilitas')],
              ['label' => 'Gizi Masyarakat', 'url' => url('kesehatan/gizi')],
              ['label' => 'Angka Harapan Hidup', 'url' => url('kesehatan/harapan-hidup')],
            ],
          ],
          [
            'label' => 'Pendidikan',
            'url'   => '#sektor',
            'icon'  => 'pendidikan',
            'submenu' => [
              ['label' => 'Partisipasi Sekolah', 'url' => url('pendidikan/partisipasi')],
              ['label' => 'Rasio Guru-Murid', 'url' => url('pendidikan/rasio-guru-murid')],
              ['label' => 'Indeks Literasi', 'url' => url('pendidikan/literasi')],
            ],
          ],
        ];
      @endphp

      <nav class="nav" aria-label="Navigasi sektor">
        @foreach ($menus as $i => $menu)
          <div class="nav-group" data-nav-group>
            <button type="button" class="nav-link" data-nav-toggle aria-expanded="false">
              <span class="nav-icon">
                @switch($menu['icon'])
                  @case('penduduk')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="2.5"/><circle cx="16.5" cy="9" r="2"/><path d="M4 17c.4-3 2.1-4.5 5-4.5S13.6 14 14 17M14.5 13c2.8 0 4.6 1.2 5 4"/></svg>
                    @break
                  @case('sosial')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="8" r="2.5"/><circle cx="16" cy="8" r="2.5"/><path d="M3.5 17c.5-3 2-4.5 4.5-4.5S12 14 12.5 17M11.5 17c.5-3 2-4.5 4.5-4.5S20 14 20.5 17"/></svg>
                    @break
                  @case('kesehatan')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="7" width="14" height="12" rx="1.5"/><path d="M9 7V4h6v3M9 13h6M12 10v6"/></svg>
                    @break
                  @case('pendidikan')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 9 9-5 9 5-9 5-9-5Z"/><path d="m6 11v5l6 3 6-3v-5"/></svg>
                    @break
                @endswitch
              </span>
              {{ $menu['label'] }}
              <span class="nav-chevron">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
              </span>
            </button>

            <ul class="submenu">
              @foreach ($menu['submenu'] as $sub)
                <li><a href="{{ $sub['url'] }}">{{ $sub['label'] }}</a></li>
              @endforeach
            </ul>
          </div>
        @endforeach
      </nav>
    </aside>

    <main class="main">
      <header class="topbar">
        <h1>Sistem Informasi Data Nasional</h1>
        <nav class="topnav"><a href="{{ url('/') }}">Dashboard</a><a href="#">Laporan</a><a href="#">Arsip</a></nav>
        <div class="actions"><span class="action">♧</span><span class="action">⚙</span><span class="action">◎</span></div>
        <nav class="mobile-nav"><a href="{{ url('/') }}">Dashboard</a><a href="{{ url('penduduk') }}">Penduduk</a></nav>
      </header>

      <section class="hero">
        <div>
          <span class="eyebrow"><i></i>Pusat Data Terintegrasi</span>
          <h2>Membangun Aceh<br>Berbasis <span>Data Presisi</span></h2>
          <p>Platform analitik terpusat yang menyajikan data komprehensif terkait kependudukan, sosial, kesehatan, dan pendidikan di Provinsi Aceh guna mendukung perumusan kebijakan yang akurat dan transparan.</p>
          <div class="hero-buttons">
            <a class="btn btn-primary" href="{{ url('penduduk') }}">Jelajahi Data <span>→</span></a>
            <a class="btn btn-secondary" href="#">⇩ &nbsp; Unduh Laporan Tahunan</a>
          </div>
        </div>
        <div class="hero-visual" aria-label="Ilustrasi jaringan data Aceh"><div class="network"></div></div>
      </section>

      <section id="sektor">
        <div class="section-title"><h3>Sektor Utama</h3></div>
        <div class="sector-grid">
          <article class="sector-card"><div class="sector-icon">♟</div><h4>Penduduk</h4><p>Data demografi, sebaran penduduk, angka kelahiran, dan kematian per kabupaten/kota.</p><a class="sector-link" href="{{ url('penduduk') }}">Akses Modul <b>→</b></a></article>
          <article class="sector-card"><div class="sector-icon">♟</div><h4>Sosial</h4><p>Indikator kesejahteraan, tingkat kemiskinan, ketenagakerjaan, dan program bantuan sosial.</p><a class="sector-link" href="#">Akses Modul <b>→</b></a></article>
          <article class="sector-card"><div class="sector-icon">✚</div><h4>Kesehatan</h4><p>Fasilitas pelayanan, gizi masyarakat, angka harapan hidup, dan pengendalian penyakit menular.</p><a class="sector-link" href="#">Akses Modul <b>→</b></a></article>
          <article class="sector-card"><div class="sector-icon">◇</div><h4>Pendidikan</h4><p>Angka partisipasi sekolah, rasio guru-murid, fasilitas pendidikan, dan indeks literasi.</p><a class="sector-link" href="#">Akses Modul <b>→</b></a></article>
        </div>
      </section>

      <footer class="footer"><span>Portal Data Warehouse Provinsi Aceh</span><span>Diskominfo Aceh — Data Terintegrasi</span></footer>
    </main>
  </div>

  {{-- Simpan landing.js di: public/assets/js/landing.js --}}
  <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>