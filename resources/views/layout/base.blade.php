<html class="no-js" lang="en">
<head>

    <meta charset="utf-8">
    <title>@yield('title')</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    @yield('script')
</head>

<body>
    <header>
        <h1>
            @yield('page_nm')
        </h1>
        <nav>
            <ul>
                <li>
                    <u><a href="@yield('link_url')">@yield('link_nm')</a></u>
                </li>
            </ul>
        </nav>
    </header>
    @yield('main_contents')
</body>