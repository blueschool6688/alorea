<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php
        $setting = \App\Models\Setting::getSettings();
    @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="ALORÉNA - Cửa hàng nước hoa chính hãng cao cấp. Khám phá bộ sưu tập nước hoa từ các thương hiệu nổi tiếng thế giới với giá tốt nhất.">
        <meta name="keywords" content="nước hoa, perfume, chính hãng, cao cấp, ALORÉNA, nước hoa nam, nước hoa nữ">
        <meta name="author" content="ALORÉNA">
        <meta name="robots" content="index, follow">

        <!-- Open Graph -->
        <meta property="og:site_name" content="ALORÉNA">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="vi_VN">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">

        <title>ALORÉNA</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="icon" type="image/x-icon" href="{{ $setting->logo_url }}">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    </head>
    <body class="font-sans antialiased">
        <div id="root"></div>
        @viteReactRefresh
        @vite(['resources/js/main.jsx'])
    </body>
</html>
