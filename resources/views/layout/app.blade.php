<!DOCTYPE html>
<html lang="en">
@include('layout.header')

<body class="sb-nav-fixed">
    <div id="app">
        @include('layout.navbar')
        <div id="layoutSidenav">
            @include('layout.sidebar')
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        @yield('content')
                    </div>
                </main>
                @include('layout.footer')
            </div>
        </div>
    </div>
    <!-- Panggil app.js hanya sekali di sini -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>