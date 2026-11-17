<div @if ($referralCode) data-referral-code="{{ $referralCode }}" @endif>

    @include('components.home.hero')

    @include('components.home.details')

    @include('components.home.about')

    @include('components.home.why-choose')

    @if ($referralCode)
        @vite('resources/js/livewire/landing-page/home.js')
    @endif

</div>
