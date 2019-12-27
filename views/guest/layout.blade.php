<?php
$webpageTitle = isset($webpage_title) ? $webpage_title : 'None';
$routeName = 'home';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $webpageTitle }} | Serverless</title>
    <link
        href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAAD9/f0AAAAAAP///wD4+PgA8fHxAPr6+gD8/PwA7u7uAP7+/gDw8PAA+fn5APLy8gD7+/sADAzoAPT09AAAAAAAh4cRGHERuYfq4d3RHd0T6nJx0R3RHRByJxEd0R3REYdxeBERERFQEiEnEREREbkXchEZLdchEXInESYScXIRh3F4eRchJ1ASYWFqRkZGGxTLEcERERwRy3gRHYeH0RF4JyHd3d3dGYdycd3d3d0QcicnHd3d0bmHcnhxEREXUHIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA"
        rel="icon" type="image/x-icon" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com" />
    <link rel="dns-prefetch" href="//use.fontawesome.com" />
    <link rel="dns-prefetch" href="//stackpath.bootstrapcdn.com" />
    <link rel="dns-prefetch" href="code.jquery.com" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
        integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous" />
    <!-- Styles -->
    <!--<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />-->
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-w6tc0TXjTUnYHwVwGgnYyV12wbRoJQo9iMlC2KdkdmVvntGgzT9jvqNEF/uKaF4m" crossorigin="anonymous" />
    <?php echo joinCss(['css/custom.css']); ?>
    <style>
    html,
    body {
        height: 100%;
    }
    </style>
    @yield('styles')
</head>

<body>
    <!-- START: Page Wrapper -->
    <table class="Layout" cellspacing="0" cellpadding="0" style="width:100%;height:100%;">
        <!-- START: Header -->
        <tr>
            <td class="Header" align="center" valign="middle" style="height:1px;">
                @include('guest.partial.header')
            </td>
        </tr>
        <!-- END: Header -->

        <!-- START: Content-->
        <tr>
            <td class="Content" align="left" valign="top" style="">
                <!-- START: Main Content -->
                @yield('content')
                <!-- END: Main Content -->
            </td>
        </tr>
        <!-- END: Content -->

        <!-- START: Footer -->
        <tr>
            <td class="Footer" align="center" valign="middle" style="height:80px;">
                @include('guest.partial.footer')
            </td>
        </tr>
        <!-- END: Footer -->
    </table>
    <!-- END: Page Wrapper -->

    @include('shared.environment')

    <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

    <script>
    @include('shared/config', [
        'websiteUrl' => \App\ Helpers\ Links::guestHome(),
        'apiUrl' => \App\ Helpers\ Links::apiUrl()
    ])
    </script>
    <script src="https://cdn.jsdelivr.net/gh/lesichkovm/web@2.4.0/initialize.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/lesichkovm/webicons@v1.4.0/webicons.ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/lesichkovm/webicons@v1.4.0/webicons.fontawesome.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/lesichkovm/webicons@v1.4.0/webicons.typicons.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/lesichkovm/webicons@v1.4.0/webicons.runtime.js"></script>
    @yield('scripts')
</body>

</html>