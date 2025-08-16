<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('admin_template/images/favicon.ico') }}">

<!-- CSS Libraries -->
<link href="{{ asset('admin_template/libs/slimselect/slimselect.css') }}" rel="stylesheet">
<link href="{{ asset('admin_template/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin_template/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin_template/libs/animate.css/animate.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin_template/libs/datatables/css/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin_template/libs/pikaday/pikaday.css') }}" rel="stylesheet">

<!-- App CSS -->
<link href="{{ asset('admin_template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('admin_template/css/icons.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('admin_template/css/app.min.css') }}" rel="stylesheet" type="text/css">

{{-- Para  dar mas funcionalidad al select css --}}
<link href="{{ asset('admin_template/libs/mobius1-selectr/selectr.min.css') }}" rel="stylesheet">

<style>
    .uppercase-input {
        text-transform: uppercase;
    }
    .uppercase-input::placeholder {
        text-transform: none;
    }

    .lowercase-input {
        text-transform: lowercase;
    }

    .lowercase-input::placeholder {
        text-transform: none;
    }


</style>
