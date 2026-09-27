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

    /* Name sits ABOVE the printed name line (the long line at y485 of
       the 1024px image → 47.4%) so the bold text never overlaps it. */

    top:43.4%;

    left:0;

    width:100%;

    text-align:center;

    font-size:20px;

    font-weight:bold;

    color:#000;

}

/*
 | Data rows measured on the background image (1536 x 1024):
 |   Name line                 y485  (x392-1198)
 |   Policy Number :  label y700-710, value line y714  (x470-700)
 |   Date of Issue :  label y733-748, value line y751  (x476-700)
 |   Coverage Type :  label y700-710, value line y714  (x1020-1266)
 |   Valid Until   :  label y733-748, value line y752  (x1019-1266)
 | Percentages are relative to the full page so the layout scales.
 */

.policy-number-value{

    position:absolute;

    /* Value line runs x470-700 (15% wide) — a 10.5px font keeps long
       TRI-xxxxx-TSxxxx-xx numbers on ONE line without wrapping. */

    top:67.9%;

    /* Nudged right so the value clears the printed ":" colon. */

    left:calc(30.6% + 2px);

    width:15.5%;

    font-size:10.5px;

    font-weight:bold;

    color:#000;

    white-space:nowrap;

}

.issue-value{

    position:absolute;

    top:71.5%;

    left:calc(31% + 1px);

    width:15%;

    font-size:10.5px;

    font-weight:bold;

    color:#000;

    white-space:nowrap;

}

.coverage-value{

    position:absolute;

    top:67.9%;

    left:66.4%;

    width:16.5%;

    font-size:10.5px;

    font-weight:bold;

    color:#000;

    white-space:nowrap;

}

.valid-value{

    position:absolute;

    top:71.6%;

    left:calc(66.3% + 1px);

    width:16.6%;

    font-size:10.5px;

    font-weight:bold;

    color:#000;

    white-space:nowrap;

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

<div class="policy-number-value">

{{ $policyNumber }}

</div>

<div class="issue-value">

{{ $dateOfIssue }}

</div>

<div class="coverage-value">

{{ $coverageType }}

</div>

<div class="valid-value">

{{ $validUntil }}

</div>



</div>

</body>

</html>
