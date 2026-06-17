{{-- filepath: resources/views/admin/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Perfume Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            overflow: hidden;
            border: 1px solid #e9ecef;
        }

        .login-header {
            background: linear-gradient(135deg, #212529 0%, #343a40 50%, #212529 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .login-header h3 {
            position: relative;
            z-index: 1;
            margin-bottom: 0.5rem;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .login-header i {
            position: relative;
            z-index: 1;
            opacity: 0.9;
        }

        .login-body {
            padding: 3rem 2rem;
            background: #ffffff;
        }

        .form-floating input {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-floating input:focus {
            border-color: #343a40;
            box-shadow: 0 0 0 0.2rem rgba(52, 58, 64, 0.15);
            background-color: white;
        }

        .form-floating label {
            color: #6c757d;
            font-weight: 500;
        }

        .btn-login {
            background: linear-gradient(135deg, #212529 0%, #343a40 50%, #212529 100%);
            border: none;
            padding: 15px;
            font-weight: 600;
            letter-spacing: 1px;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #343a40 0%, #495057 50%, #343a40 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .invalid-feedback {
            color: #495057;
            font-weight: 500;
        }

        .form-control.is-invalid {
            border-color: #6c757d;
        }

        /* Custom scrollbar for consistency */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #343a40;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #495057;
        }

        /* Loading animation */
        .btn-login.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .login-header {
                padding: 2rem 1.5rem;
            }

            .login-body {
                padding: 2rem 1.5rem;
            }

            .login-header h3 {
                font-size: 1.5rem;
            }

            .login-header i {
                font-size: 2rem;
            }
        }

        /* Subtle animations */
        .login-card {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-floating {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-shield-alt fa-3x mb-3"></i>
                        <h3>Admin Panel</h3>
                        <p class="mb-0 opacity-75">Secure Access</p>
                    </div>

                    <div class="login-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <div><i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
                            @csrf

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" placeholder="name@example.com"
                                       value="{{ old('email') }}" required>
                                <label for="email"><i class="fas fa-user me-2"></i>Email Address</label>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" placeholder="Password" required>
                                <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-login w-100" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Đăng Nhập
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>
                                Secure Admin Access
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Add loading state to login button
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Đang xử lý...';
        });

        // Add focus animations
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
