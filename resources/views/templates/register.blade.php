<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    @if(env('APP_ENV') == 'prod')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    @else
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.js"></script>
        @endif

                <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

        <style type="text/css">
            body {
                background-color: <?= $brand == "brightthinker" ? "#E8930C" : "#D8DFE2" ?>;
            }

            #SubmissionFormBox {
                padding: 15px;
                background-color: #FFF;
            }

            #ProfessorEd {
                margin-top: 28px;
            }

            .roundedBox {
                border-radius: 18px;

                /* Prevent background color leak outs */
                -webkit-background-clip: padding-box;
                -moz-background-clip: padding;
                background-clip: padding-box;
            }

            .testimonialBox {
                background-color: rgba(51, 51, 51, 0.4);
                padding: 15px;
            }

            .submitBtn {
                color: #FFF;
                background-color: #FFB600;
                border-color: #E8930C;
                font-weight: 700;
            }

            .submitBtn:hover,
            .submitBtn:focus,
            .submitBtn:active,
            .submitBtn.active,
            .open .dropdown-toggle.submitBtn {
                color: #FFF;
                background-color: #E8930C;
                border-color: #E8930C;
            }

            .submitBtn:active,
            .submitBtn.active,
            .open .dropdown-toggle.submitBtn {
                background-image: none;
            }

            .submitBtn.disabled,
            .submitBtn[disabled],
            fieldset[disabled] .submitBtn,
            .submitBtn.disabled:hover,
            .submitBtn[disabled]:hover,
            fieldset[disabled] .submitBtn:hover,
            .submitBtn.disabled:focus,
            .submitBtn[disabled]:focus,
            fieldset[disabled] .submitBtn:focus,
            .submitBtn.disabled:active,
            .submitBtn[disabled]:active,
            fieldset[disabled] .submitBtn:active,
            .submitBtn.disabled.active,
            .submitBtn[disabled].active,
            fieldset[disabled] .submitBtn.active {
                background-color: #FFB600;
                border-color: #E8930C;
            }

            .submitBtn .badge {
                color: #FFB600;
                background-color: #FFF;
            }
        </style>
</head>
<body>
<div class="container">
    @yield('body')
</div>
@yield('script')
</body>
</html>