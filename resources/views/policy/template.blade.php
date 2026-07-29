<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<style>
@page {
    margin:0;
}

body{
    margin:0;
    font-family: DejaVu Sans;
}

.page{
    position:relative;
    width:816px;
    height:1056px;
}

.bg{
    position:absolute;
    top:0;
    left:0;
    width:816px;
    height:1056px;
}

.field{
    position:absolute;
    font-size:12px;
    font-weight:bold;
}
</style>

</head>
<body>

<div class="page">

    <!-- Background -->
    <img
        class="bg"
        src="{{ public_path('images/certificate/policy.png') }}">

    <!-- PRIMARY OWNER -->

    <div class="field" style="left:140px;top:168px;">
        {{ strtoupper($user->lastname) }}
    </div>

    <div class="field" style="left:140px;top:185px;">
        {{ strtoupper($user->middlename) }}
    </div>

    <div class="field" style="left:140px;top:202px;">
        {{ strtoupper($user->firstname) }}
    </div>

    <div class="field" style="left:140px;top:219px;">
        {{ \Carbon\Carbon::parse($user->birthdate)->format('M d, Y') }}
    </div>

@php
    $address = wordwrap(strtoupper($user->address), 32, "\n", true);
@endphp

<div
    class="field"
    style="
        left:140px;
        top:219px;
        width:310px;
        font-size:12px;
        line-height:14px;
        white-space:pre-line;
    ">
    {{ $address }}
</div>

    <!-- Effective Date -->
    <div class="field" style="left:140px;top:282px;">
        {{ $effectiveDate->format('M d, Y') }}
    </div>

    <!-- Expiry Date -->
    <div class="field" style="left:140px;top:298px;">
        {{ $expiryDate->format('M d, Y') }}
    </div>

    <!-- Policy Number -->
    <div class="field" style="left:160px;top:322px;color:#fff;">
        {{ $policyNumber }}
    </div>


    <!-- BENEFICIARY #1 -->

    <div class="field" style="left:450px;top:177px;">
        {{ $beneficiaries[0]->fullname ?? '' }}
    </div>

    <div class="field" style="left:516px;top:195px;">
        {{ isset($beneficiaries[0])
            ? \Carbon\Carbon::parse($beneficiaries[0]->birthdate)->format('M d, Y')
            : '' }}
    </div>


     <!-- BENEFICIARY #2 -->

    <div class="field" style="left:450px;top:231px;">
        {{ $beneficiaries[1]->fullname ?? '' }}
    </div>

    <div class="field" style="left:516px;top:250px;">
        {{ isset($beneficiaries[1])
            ? \Carbon\Carbon::parse($beneficiaries[0]->birthdate)->format('M d, Y')
            : '' }}
    </div>

        <!-- BENEFICIARY #3 -->

    <div class="field" style="left:450px;top:289px;">
        {{ $beneficiaries[2]->fullname ?? '' }}
    </div>

    <div class="field" style="left:516px;top:308px;">
        {{ isset($beneficiaries[2])
            ? \Carbon\Carbon::parse($beneficiaries[0]->birthdate)->format('M d, Y')
            : '' }}
    </div>

</div>

</body>
</html>
