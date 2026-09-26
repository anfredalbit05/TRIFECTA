<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<style>

@page{
    margin:0;
}

body{

    margin:0;
    padding:0;
    font-family: DejaVu Sans;

}

.page{

    position:relative;

    width:11in;
    height:8.5in;

}

.background{

    position:absolute;
    top:0;
    left:0;

    width:100%;
    height:100%;

}

.name{

    position:absolute;

    /* Sits exactly on the printed name line of the background
       (line at 44.4% of image height → 362px of the 816px page) */

    top:350px;

    left:0;

    width:100%;

    text-align:center;

    font-size:22px;

    font-weight:bold;

    color:#000;

}

.date{

    position:absolute;

    bottom:105px;

    left:175px;

    font-size:18px;

}

.cert{

    position:absolute;

    bottom:105px;

    right:170px;

    font-size:18px;

}

</style>

</head>

<body>

<div class="page">

<img
    src="{{ public_path('images/certificate/certificate.jpg') }}"
    class="background">

<div class="name">

{{ $fullname }}

</div>



</div>

</body>

</html>
