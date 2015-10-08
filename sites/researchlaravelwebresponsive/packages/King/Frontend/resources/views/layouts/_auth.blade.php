<!DOCTYPE html>
<html>
    <head>
        <title> @yield('title') - Suris - The world of online store</title>
        <meta charset="UTF-8">
        <meta id="viewport" name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <meta name="msapplication-tap-highlight" content="no"/>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <link rel="stylesheet" href="{{ asset('packages/king/frontend/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('packages/king/frontend/css/font-awesome.css') }}">
        <link rel="stylesheet" href="{{ asset('packages/king/frontend/css/common.css') }}">
        <link rel="stylesheet" href="{{ asset('packages/king/frontend/css/style.css') }}">
    </head>
    <body class="auth-page">

        <div class="_fwfl header">
            <div class="_mw970 _ma _mt15 header-inside">
                @yield('head-link')
            </div>
        </div>

        <div class="_fwfl auth-container">
            <div class="_mw970 _ma">
                @yield('body')
            </div>
        </div>

        <script src="{{ asset('packages/king/frontend/js/jquery_v1.11.1.js') }}"></script>
        <script src="{{ asset('packages/king/frontend/js/bootstrap.js') }}"></script>
        <script src="{{ asset('packages/king/frontend/js/script.js') }}"></script>
    </body>
</html>