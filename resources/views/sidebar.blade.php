{{-- resources/views/layouts/sidebar.blade.php --}}

{{-- Contoh struktur dasar sidebar --}}
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Workout Kos</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/dashboard" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- Bagian ini hanya akan terlihat oleh Admin --}}
                @if (Auth::check() && Auth::user()->isAdmin())
                <li class="nav-item">
                    <a href="/admin/users" class="nav-link"> {{-- Sesuaikan URL jika berbeda, misalnya '/users' atau '/admin/manajemen-pengguna' --}}
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Kelola Admin</p>
                    </a>
                </li>
                @endif

                {{-- Contoh menu lain (sesuai kebutuhan aplikasi Anda) --}}
                <li class="nav-item">
                    <a href="/tutorial-workouts" class="nav-link">
                        <i class="nav-icon fas fa-dumbbell"></i>
                        <p>Tutorial Workouts</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/jadwal-workouts" class="nav-link">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Jadwal Workouts</p>
                    </a>
                </li>

                {{-- Tombol Logout --}}
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="#" class="nav-link" onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>