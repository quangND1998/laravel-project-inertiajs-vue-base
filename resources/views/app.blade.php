<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="locale" content="{{ App::getLocale() }}" />
    <base href="{{ asset('') }}">
    <title>Forum</title>
    <link rel="stylesheet" type="text/css" href="backend/css/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="backend/css/font-awesome/6.0.0/css/all.min.css" />
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    <script src="{{ mix('/js/app.js') }}" defer></script>

    @routes
    @inertiaHead

</head>

<body>
    @inertia
    <script src="backend/js/jquery.3.2.1.min.js"></script>
</body>

</html>
