<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image" href="{{ asset("utils/images/icon.png") }}">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"></script>

    @yield('title')

    <link rel="stylesheet" href="{{ asset("utils/css/main.css") }}">

    @yield('resources')

</head>
<body>
{{-- Je prévois l'injection de quelque chose ici (optionnel !) --}}
@yield('content')
</body>
@yield('js_page')
</html>
