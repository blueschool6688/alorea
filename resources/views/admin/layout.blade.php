{{-- filepath: resources/views/admin/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Perfume Store</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 10px;
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        .stats-card .card-body {
            padding: 1.5rem;
        }
        .stats-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: bold;
        }
        .navbar-brand {
            font-weight: bold;
            color: #495057 !important;
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="sidebar-brand">
                    <h4><i class="fas fa-crown"></i> Admin Panel</h4>
                </div>

                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"
                               href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>

                       <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}"
                               href="{{ route('admin.products.index') }}">
                                <i class="fas fa-box"></i>
                                Products
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}"
                               href="{{ route('admin.categories.index') }}">
                                <i class="fas fa-tags"></i>
                                Categories
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                               href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users"></i>
                                Users
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}"
                               href="{{ route('admin.collections.index') }}">
                                <i class="fas fa-ticket-alt"></i>
                                Collections
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.flash-sales*') ? 'active' : '' }}"
                               href="{{ route('admin.flash-sales.index') }}">
                                <i class="fas fa-chart-bar"></i>
                                Flash Sales
                            </a>
                        </li>


                        {{-- <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}"
                               href="{{ route('admin.blog-categories.index') }}">
                                <i class="fas fa-blog"></i>
                                Blog Categories
                            </a>
                        </li> --}}

                         <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}"
                               href="{{ route('admin.blogs.index') }}">
                                <i class="fas fa-blog"></i>
                                Blog
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.scents.*') ? 'active' : '' }}"
                               href="{{ route('admin.scents.index') }}">
                                <i class="fas fa-blog"></i>
                                Scents
                            </a>
                        </li>



                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"
                               href="{{ route('admin.orders.index') }}">
                                <i class="fas fa-shopping-cart"></i>
                                Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}"
                               href="{{ route('admin.contact.index') }}">
                                <i class="fas fa-ticket-alt"></i>
                                Contact
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.show') }}">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top navbar -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('page-title', 'Dashboard')</h1>

                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="{{ route('admin.clear-cache') }}" class="btn btn-primary me-4">Clear Cache</a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i>
                                {{ Auth::guard('admin')->user()->name }}
                            </button>
                            <ul class="dropdown-menu">
                                {{-- <li><a class="dropdown-item" href="{{ route('admin.settings') }}">
                                    <i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li> --}}
                                    <hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Auto hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Toggle status function
        function toggleStatus(url, element) {
            $.ajax({
                url: url,
                type: 'POST',
                success: function(response) {
                    if(response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Error occurred while updating status');
                }
            });
        }

        // Delete confirmation
        function confirmDelete(form) {
            if (confirm('Are you sure you want to delete this item?')) {
                form.submit();
            }
            return false;
        }
    </script>

    @stack('scripts')
</body>
</html>
