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
        src="{{ public_path('images/certificate/policy.jpeg') }}">

    <!-- PRIMARY OWNER -->

    <div class="field" style="left:140px;top:188px;">
        {{ strtoupper($user->lastname) }}
    </div>

    <div class="field" style="left:140px;top:205px;">
        {{ strtoupper($user->middlename) }}
    </div>

    <div class="field" style="left:140px;top:222px;">
        {{ strtoupper($user->firstname) }}
    </div>

    <div class="field" style="left:140px;top:239px;">
        {{ \Carbon\Carbon::parse($user->birthdate)->format('M d, Y') }}
    </div>

@php
    $address = strtoupper($user->address ?? '');

    // Determine font size based on address length
    $addressLength = strlen($address);

    if ($addressLength > 90) {
        $addressFontSize = 9;
        $addressLineHeight = 11;
    } elseif ($addressLength > 75) {
        $addressFontSize = 10;
        $addressLineHeight = 12;
    } elseif ($addressLength > 55) {
        $addressFontSize = 11;
        $addressLineHeight = 13;
    } else {
        $addressFontSize = 10;
        $addressLineHeight = 14;
    }

    // Wrap address without cutting words
    $address = wordwrap($address, 38, "\n", false);
@endphp

<div
    class="field"
    style="
        left:140px;
        top:241px;
        width:310px;
        font-size:{{ $addressFontSize }}px;
        line-height:{{ $addressLineHeight }}px;
        white-space:pre-line;
        max-height:34px;
        overflow:hidden;
    "
>
    {{ $address }}
</div>

        <!-- Effective Date -->
    <div class="field" style="left:140px;top:298px;">
        {{ $policy_effectivity_date
    ? \Carbon\Carbon::parse($policy_effectivity_date)->format('M d, Y')
    : ''
}}
    </div>

    <!-- Expiry Date -->
    <div class="field" style="left:140px;top:316px;">
        {{ $policy_expiration_date
    ? \Carbon\Carbon::parse($policy_expiration_date)->format('M d, Y')
    : ''
}}
    </div>

    <!-- Policy Number -->
    <div class="field" style="left:157px;top:339px;color:#fff;">
        {{ $policyNumber }}
    </div>


    <!-- BENEFICIARY #1 -->

    <div class="field" style="left:445px;top:194px;">
        {{ $beneficiaries[0]->fullname ?? '' }}
    </div>

    <div class="field" style="left:515px;top:210px;">
        {{ isset($beneficiaries[0])
            ? \Carbon\Carbon::parse($beneficiaries[0]->birthdate)->format('M d, Y')
            : '' }}
    </div>


     <!-- BENEFICIARY #2 -->

    <div class="field" style="left:445px;top:250px;">
        {{ $beneficiaries[1]->fullname ?? '' }}
    </div>

    <div class="field" style="left:515px;top:266px;">
        {{ isset($beneficiaries[1])
            ? \Carbon\Carbon::parse($beneficiaries[1]->birthdate)->format('M d, Y')
            : '' }}
    </div>

        <!-- BENEFICIARY #3 -->

    <div class="field" style="left:445px;top:305px;">
        {{ $beneficiaries[2]->fullname ?? '' }}
    </div>

    <div class="field" style="left:515px;top:321px;">
        {{ isset($beneficiaries[2])
            ? \Carbon\Carbon::parse($beneficiaries[2]->birthdate)->format('M d, Y')
            : '' }}
    </div>

</div>

</body>
</html>
