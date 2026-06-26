<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MES LienChau</title>
    <!-- No Favicon as requested -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; }
        .card-header { font-weight: bold; }
        .navbar-brand { font-weight: bold; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    @if(Auth::check() || Auth::guard('admin')->check())
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            @php
                $isAdminArea = request()->is('admin*');
            @endphp
            <a class="navbar-brand d-flex align-items-center" href="{{ $isAdminArea ? route('admin.users.index') : route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" height="30" class="me-2 bg-white rounded p-1">
                <span class="fw-bold">MES LienChau</span>
            </a>
            
            @if($isAdminArea && Auth::guard('admin')->check())
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-bold text-white' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="fa-solid fa-users me-1"></i> Quản lý Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.approvers.*') ? 'active fw-bold text-white' : '' }}" href="{{ route('admin.approvers.index') }}">
                        <i class="fa-solid fa-user-shield me-1"></i> Phân quyền phê duyệt
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active fw-bold text-white' : '' }}" href="{{ route('admin.permissions.index') }}">
                        <i class="fa-solid fa-users-gear me-1"></i> Phân quyền hệ thống
                    </a>
                </li>
            </ul>
            @endif
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-light d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-user fs-4 me-2"></i>
                        <strong>
                            @if($isAdminArea && Auth::guard('admin')->check())
                                {{ Auth::guard('admin')->user()->full_name }}
                            @elseif(Auth::check())
                                {{ Auth::user()->full_name }}
                            @else
                                Guest
                            @endif
                        </strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        @if(!$isAdminArea && Auth::check())
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile.delegate') }}">
                                <i class="fa-solid fa-user-clock me-2 text-primary"></i> Uỷ quyền duyệt đơn
                            </a>
                        </li>
                        @endif
                        <li>
                            <a class="dropdown-item py-2" href="{{ $isAdminArea ? route('admin.password') : route('profile.password') }}">
                                <i class="fa-solid fa-key me-2 text-warning"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ $isAdminArea ? route('admin.logout') : route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="container-fluid flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">@yield('title')</h2>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    @else
        @yield('content')
    @endif

    <footer class="bg-light text-center py-3 mt-auto shadow-sm">
        <span class="text-muted">Copyright &copy; 2026 MES LienChau. Powered by IT LC</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery and Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('scripts')
</body>
</html>