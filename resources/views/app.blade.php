<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <title>Laravel</title>
        <script>
            window._asset = '{{ asset('') }}';
          </script>

          
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">



        @vite([
            'resources/js/app.js',
            'resources/js/main.js',
            'resources/lib/easing/easing.js',
            'resources/lib/easing/easing.min.js',
            'resources/lib/owlcarousel/owl.carousel.js',
            'resources/lib/owlcarousel/owl.carousel.min.js',
            'resources/lib/tempusdominus/css/tempusdominus-bootstrap-4.css',
            'resources/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css',
            'resources/lib/waypoints/waypoints.min.js',
            'resources/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css',
            'resources/lib/owlcarousel/assets/owl.carousel.min.css',
            'resources/lib/chart.min.js',
            'resources/lib/tempusdominus/js/moment.min.js',
            'resources/lib/moment-timezone.min.js',
            'resources/lib/tempusdominus-bootstrap-4.min.js',
            'resources/lib/',
            'resources/css/bootstrap.min.css',
            'resources/css/style.css'
       ])
    </head>
    <body>
        <div id="app"></div>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>
