<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>7SCOUT</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link href="{{ asset('/plugins/webix/webix.css') }}" rel="stylesheet">
    <script src="{{ asset('/plugins/webix/webix.js') }}"></script>
    <style>
        .active_badge {
            background-color: #3490dc;
            border-radius: 10px;
            height: 34px;
            padding: 1px 10px;
            color: white;
            font-weight: 500;
        }
        .inactive_badge {
            background-color: darkgrey;
            border-radius: 10px;
            height: 34px;
            padding: 1px 10px;
            color: white;
            font-weight: 500;
        }

        .success_badge {
            background-color: #2196F3;
            border-radius: 10px;
            height: 34px;
            padding: 2px 10px;
            color: black;
            font-weight: 500;
        }

        .warning_badge {
            background-color: orange;
            border-radius: 10px;
            height: 34px;
            padding: 2px 10px;
            color: black;
            font-weight: 500;
        }

        .danger_badge {
            background-color: red;
            border-radius: 10px;
            height: 34px;
            padding: 2px 10px;
            color: black;
            font-weight: 500;
        }


        .purple_badge {
            background-color: purple;
            border-radius: 10px;
            height: 34px;
            padding: 2px 10px;
            color: white;
            font-weight: 500;
        }

        .green_lighten_badge {
            background-color: #81C784;
            border-radius: 10px;
            height: 34px;
            padding: 2px 10px;
            color: white;
            font-weight: 500;
        }

        .grey_badge {
            background-color: #eaeaea;
            border-radius: 10px;
            height: 34px;
            padding: 2px 10px;
            color: black;
            font-weight: 500;
        }

        .btn {
            align-items: center;
            border: 1px solid !important;
            border-radius: 4px;
            display: inline-flex;
            flex: 0 0 auto;
            font-weight: 500;
            letter-spacing: 0.0892857143em;
            justify-content: center;
            outline: 0;
            position: relative;
            text-decoration: none;
            text-indent: 0.0892857143em;
            text-transform: uppercase;
            transition-duration: 0.28s;
            transition-property: box-shadow, transform, opacity;
            transition-timing-function: cubic-bezier(0.82, 0.04, 0.2, 1);
            user-select: none;
            vertical-align: middle;
            white-space: nowrap;
            background-color: transparent;
            min-width: 50px;
            line-height: 22px;
            padding-left: 3px;
            padding-right: 3px;
        }

        .btn-primary{
            color: #1976d2 !important;
        }

        .btn-secondary{
            color: darkgrey !important;
        }

        .btn-error{
            color: #ff5252 !important;
        }

        .btn-primary:hover {
            background-color: #E3F2FD;
        }

        .btn-secondary:hover {
            background-color: #efefef;
        }

        .btn-error:hover {
            background-color: #FFCDD2;
        }

        .webix_multilist .wxi-checkbox-blank, .webix_multilist .wxi-checkbox-marked {
            vertical-align: middle;
        }
    </style>
</head>
<body >
<div id="app">
    <app>

    </app>

</div>
<script src="{{ asset('js/app.js') }}"></script>
<script>

    webix.i18n.dateFormat = "%d.%m.%Y";
    webix.i18n.parseFormat = "%d.%m.%Y";
    webix.i18n.setLocale("en-EN");

</script>
</body>
</html>
