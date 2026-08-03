<div>

    @include('components.home.hero')

    @include('components.home.details')

    @include('components.home.about')

    @include('components.home.why-choose')

    @if ($referralCode)
        <script>
            document.addEventListener('livewire:init', () => {
                setTimeout(() => {
                    Livewire.dispatch('open-application-modal', {
                        upline: @js($referralCode)
                    });
                }, 500);
            });
        </script>
    @endif

</div>
