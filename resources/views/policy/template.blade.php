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
    font-size:11px;
    font-weight:bold;
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

    <div class="field" style="left:140px;top:163px;">
        {{ strtoupper($user->lastname) }}
    </div>

    <div class="field" style="left:140px;top:176px;">
        {{ strtoupper($user->middlename) }}
    </div>

    <div class="field" style="left:140px;top:190px;">
        {{ strtoupper($user->firstname) }}
    </div>

    <div class="field" style="left:126px;top:202px;">
        {{ \Carbon\Carbon::parse($user->birthdate)->format('M d, Y') }}
    </div>

@php
    $address = strtoupper(trim($user->address ?? ''));
@endphp

<div
    class="field"
    style="
        left:121px;
        top:216px;
        width:350px;
        height:13px;
        display:block;
        box-sizing:border-box;
        font-size:8px;
        line-height:13px;
        font-weight:bold;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:clip;
    "
>
    {{ $address }}
</div>

        <!-- Effective Date -->
    <div class="field" style="left:153px;top:229px;">
        {{ $policy_effectivity_date
    ? \Carbon\Carbon::parse($policy_effectivity_date)->format('M d, Y')
    : ''
}}
    </div>

    <!-- Expiry Date -->
    <div class="field" style="left:135px;top:242px;">
        {{ $policy_expiration_date
    ? \Carbon\Carbon::parse($policy_expiration_date)->format('M d, Y')
    : ''
}}
    </div>

    <!-- Policy Number -->
    <div class="field" style="left:198px;top:263px;">
        {{ $policyNumber }}
    </div>



<!-- BENEFICIARY #1 -->

<div class="field"
     style="left:612px; top:168px; width:190px; height:13px;
            font-size:9px; line-height:13px;
            font-weight:bold; white-space:nowrap; overflow:hidden;">
    {{ $beneficiaries[0]->fullname ?? '' }}
</div>

<div class="field" style="left:543px;top:182px;">
    {{ isset($beneficiaries[0])
        ? \Carbon\Carbon::parse($beneficiaries[0]->birthdate)->format('M d, Y')
        : '' }}
</div>


<!-- BENEFICIARY #2 -->

<div class="field"
     style="left:614px; top:199px; width:190px; height:13px;
            font-size:9px; line-height:13px;
            font-weight:bold; white-space:nowrap; overflow:hidden;">
    {{ $beneficiaries[1]->fullname ?? '' }}
</div>

<div class="field" style="left:543px;top:215px;">
    {{ isset($beneficiaries[1])
        ? \Carbon\Carbon::parse($beneficiaries[1]->birthdate)->format('M d, Y')
        : '' }}
</div>


<!-- BENEFICIARY #3 -->

<div class="field"
     style="left:616px; top:238px; width:190px; height:13px;
            font-size:9px; line-height:13px;
            font-weight:bold; white-space:nowrap; overflow:hidden;">
    {{ $beneficiaries[2]->fullname ?? '' }}
</div>

<div class="field" style="left:545px;top:254px;">
    {{ isset($beneficiaries[2])
        ? \Carbon\Carbon::parse($beneficiaries[2]->birthdate)->format('M d, Y')
        : '' }}
</div>


</div>

</body>
</html>
