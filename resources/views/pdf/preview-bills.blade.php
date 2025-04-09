<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <title></title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}" />
    <style>
        .container .header .logo {
            background-image: url("{{ public_path('images/logo.jpg') }}");
            background-repeat: no-repeat;
            min-width: 280px;
            height: 80px;
        }
        .container .footer .payment-coupon .scissors {
            height: 24px;
            margin: auto auto;
            background-image: url("{{ public_path('images/scissors.png') }}");
            background-repeat: no-repeat;
            background-position: right;
            position: relative;
            overflow: hidden;
            background-size: 20px 20px;
            top: 12px;
            right: 10px;
        }
    </style>
</head>
<body class="page">
    <div class="container">
        <div id="header">
            @include('pdf.header')
        </div>
        <div class="content">
            @include('pdf.content')
        </div>
        <div id="footer">
            @include('pdf.footer')
        </div>
    </div>
</body>

</html>


