<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{asset('favicon.png')}}" rel="icon" type="image/x-icon">
    <title>{{config('app.name')}}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,600;1,400&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="{{mix('/css/app.css')}}">
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function () {
            OneSignal.init({
                appId: "0ee55d6a-0101-4da0-b9c3-e012ad604212",
            });
        });
    </script>
</head>
<div id="app">
    <app></app>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}&libraries=places"></script>
<script src="{{mix('/js/app.js')}}" defer></script>
<script src="https://js.stripe.com/v3/"></script>

</html>
