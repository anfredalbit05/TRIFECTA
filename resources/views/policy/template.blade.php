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
    font-size:9px;
    font-style: normal;
}

</style>

</head>
<body>

<div class="page">

    <!-- Background -->
    <img
        class="bg"
        src="{{ public_path('images/certificate/policy.jpg') }}">

    <!-- PRIMARY OWNER -->

    <div class="field" style="left:143px;top:152px;">
        {{ strtoupper($user->lastname) }}
    </div>

    <div class="field" style="left:144px;top:164px;">
        {{ strtoupper($user->middlename) }}
    </div>

    <div class="field" style="left:133px;top:176px;">
        {{ strtoupper($user->firstname) }}
    </div>

    <div class="field" style="left:125px;top:188px;">
        {{ \Carbon\Carbon::parse($user->birthdate)->format('M d, Y') }}
    </div>

@php
    $address = strtoupper(trim($user->address ?? ''));
@endphp

<div
    class="field"
    style="
        left:115px;
        top:198px;
        width:350px;
        height:13px;
        display:block;
        box-sizing:border-box;
        font-size:8px;
        line-height:13px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:clip;
    "
>
    {{ $address }}
</div>

        <!-- Effective Date -->
    <div class="field" style="left:157px;top:212px;">
        {{ $policy_effectivity_date
    ? \Carbon\Carbon::parse($policy_effectivity_date)->format('M d, Y')
    : ''
}}
    </div>

    <!-- Expiry Date -->
    <div class="field" style="left:140px;top:223.5px;">
        {{ $policy_expiration_date
    ? \Carbon\Carbon::parse($policy_expiration_date)->format('M d, Y')
    : ''
}}
    </div>

    <!-- Policy Number -->
    <div class="field" style="left:188px;top:241px;">
        {{ $policyNumber }}
    </div>


    <!-- BENEFICIARY #1 -->

    <div class="field" style="left:450px;top:155px;">
        {{ $beneficiaries[0]->fullname ?? '' }}
    </div>

    <div class="field" style="left:531px;top:168px;">
        {{ isset($beneficiaries[0])
            ? \Carbon\Carbon::parse($beneficiaries[0]->birthdate)->format('M d, Y')
            : '' }}
    </div>


     <!-- BENEFICIARY #2 -->

    <div class="field" style="left:450px;top:183px;">
        {{ $beneficiaries[1]->fullname ?? '' }}
    </div>

    <div class="field" style="left:531px;top:198px;">
        {{ isset($beneficiaries[1])
            ? \Carbon\Carbon::parse($beneficiaries[1]->birthdate)->format('M d, Y')
            : '' }}
    </div>

        <!-- BENEFICIARY #3 -->

    <div class="field" style="left:450px;top:213px;">
        {{ $beneficiaries[2]->fullname ?? '' }}
    </div>

    <div class="field" style="left:531px;top:228px;">
        {{ isset($beneficiaries[2])
            ? \Carbon\Carbon::parse($beneficiaries[2]->birthdate)->format('M d, Y')
            : '' }}
    </div>

</div>

</body>
</html>
