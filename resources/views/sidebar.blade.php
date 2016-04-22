<!-- Sidebar start -->
<nav class="sidebar">
    @include('sidebar_item', ['route' => 'panel_utama', 'icon' => 'tasks', 'label' => 'Panel Utama'])
    <hr/>
    @include('sidebar_item', ['route' => 'nilai', 'icon' => 'check-square-o', 'label' => 'Nilai'])
    @include('sidebar_item', ['route' => 'absensi', 'icon' => 'circle', 'label' => 'Absensi'])
    @include('sidebar_item', ['route' => 'cetak', 'icon' => 'print', 'label' => 'Cetak'])
    <hr/>
    @include('sidebar_item', ['route' => 'siswa', 'icon' => 'graduation-cap', 'label' => 'Siswa'])
    @include('sidebar_item', ['route' => 'kelas', 'icon' => 'bookmark', 'label' => 'Kelas'])
    @include('sidebar_item', ['route' => 'guru', 'icon' => 'users', 'label' => 'Guru'])
    @include('sidebar_item', ['route' => 'pelajaran', 'icon' => 'book', 'label' => 'Pelajaran'])
    @include('sidebar_item', ['route' => 'semester', 'icon' => 'calendar', 'label' => 'Semester'])
    @include('sidebar_item', ['route' => 'pengaturan', 'icon' => 'cogs', 'label' => 'Pengaturan'])
    <hr/>
    @include('sidebar_item', ['route' => 'lama.akhir', 'icon' => 'database', 'label' => 'Data Lama'])
    @include('sidebar_item', ['route' => 'ralat', 'icon' => 'pencil', 'label' => 'Ralat'])
    <hr/>
    <a href='{{ route('keluar') }}'><li class="red"><i class="fa fa-power-off"></i>Keluar</li></a>
</nav>
<!-- Sidebar end -->
