<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<div class="auth-container">
@yield('content')
</div>
</body>
</html>