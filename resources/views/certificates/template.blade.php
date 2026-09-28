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

    top:67.3%;

    /* Nudged right so the value clears the printed ":" colon. */

    left:calc(30.6% + 2px);

    width:15.5%;

    font-size:14px;

    font-weight:bold;

    color:#000;

    white-space:nowrap;

}

.issue-value{

    position:absolute;

    top:70.5%;

    left:calc(31% + 1px);

    width:15%;

    font-size:14px;

    font-weight:bold;

    color:#000;

    white-space:nowrap;

}


.coverage-value{
    position:absolute;

    top:64.7%;
    left:66.4%;

    width:16.5%;

    font-size:14px;
    font-weight:bold;
    color:#000;

    line-height:1.2;
    white-space:normal;
    text-align:left;
}



.valid-value{

    position:absolute;

    top:70.5%;

    left:calc(66.3% + 1px);

    width:16.6%;

    font-size:14px;

    font-weight:bold;

    color:#000;

    white-space:nowrap;

}

/*
 | Signature images, overlaid ABOVE each printed name block (the
 | artwork already prints "RENANTE S. ROLES, MPA, LFA / CHIEF EXECUTIVE
 | OFFICER" on the left and the COO block on the right; their lines
 | sit at y828 of the 1536x1024 image = 80.9%). Each image is
 | contain-fit into the white space between the policy rows and the
 | printed line so the ink never covers the name below.
 |
 | Geometry (percent of the 1536 x 1024 page):
 |   LEFT  (CEO): line x305-696  → signature x19.9-45.3%, y 72.3-80.6%
 |   RIGHT (COO): line x887-1266 → signature x57.7-82.3%, y 72.3-80.6%
 */

.sig-img{

    position:absolute;

    top:72.3%;

    height:8.3%;

    object-fit:contain;

    object-position:center bottom;



}

.sig-ceo{

    left:19.9%;

    width:25.4%;
    height: 6.4%;
    margin-top: 20px;

}

.sig-coo{

    left:57.7%;

    width:24.6%;

    height: 6.4%;
    margin-top: 20px;

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

{{-- SIGNATORIES — signature ink above each printed name block.
     Rendered only when that officer has a signature on file; the
     artwork's printed name + title remain visible either way. --}}

@if (($ceo['signature'] ?? null) !== null)

<img src="{{ $ceo['signature'] }}" class="sig-img sig-ceo">

@endif

@if (($coo['signature'] ?? null) !== null)

<img src="{{ $coo['signature'] }}" class="sig-img sig-coo">

@endif



</div>

</body>

</html>
