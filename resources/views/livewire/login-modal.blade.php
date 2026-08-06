<div>
    @if($openLogin)
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 p-3 sm:p-4 backdrop-blur-sm overflow-y-auto">

            <!-- Modal Card -->
            <div class="bg-white rounded-3xl w-full max-w-md my-auto shadow-2xl border border-slate-100 overflow-hidden relative">

                <!-- Close Button -->
                <button
                    wire:click="close"
                    type="button"
                    class="absolute top-4 right-4 w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-rose-100 hover:text-rose-600 font-bold transition z-10">
                    ✕
                </button>

                <!-- Header Banner -->
                <div class="bg-[#0B2A5B] p-6 sm:p-8 text-center text-white relative">
                    <div class="w-12 h-12 rounded-2xl bg-[#D4A017] flex items-center justify-center font-black text-[#0B2A5B] text-xl mx-auto shadow-lg mb-3">
                        D
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black">Welcome Back</h2>
                    <p class="text-xs text-slate-300 mt-1">Access your Dayongan member portal</p>
                </div>

                <!-- Form Body -->
                <form wire:submit.prevent="login" class="p-6 sm:p-8 space-y-4">

                    <!-- Email Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email or Username or Mobile Number</label>
                        <input
                            wire:model="email"
                            type="text"
                            placeholder="Enter your email or phone"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:border-[#0B2A5B] focus:outline-none transition">
                        @error('email') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-slate-700">Password</label>
                            <a href="#" class="text-xs text-[#0B2A5B] font-semibold hover:underline">Forgot password?</a>
                        </div>
                        <input
                            wire:model="password"
                            type="password"
                            placeholder="••••••••"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:border-[#0B2A5B] focus:outline-none transition">
                        @error('password') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                wire:model="remember"
                                type="checkbox"
                                class="w-4 h-4 rounded text-[#0B2A5B] focus:ring-[#0B2A5B] border-slate-300">
                            <span class="text-xs text-slate-600 font-medium">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit Login Button -->
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full py-3.5 bg-[#0B2A5B] hover:bg-[#071D3F] text-white font-bold text-sm rounded-xl shadow-lg transition duration-200 mt-2 flex items-center justify-center disabled:opacity-75 disabled:cursor-not-allowed cursor-pointer">

                        <!-- Normal State -->
                        <span wire:loading.remove wire:target="login">
                            Sign In
                        </span>

                        <!-- Loading State -->
                        <div wire:loading.flex wire:target="login" class="items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Signing In...</span>
                        </div>

                    </button>

                    <!-- Divider -->
                    <div class="relative my-4 flex items-center justify-center">
                        <div class="border-t border-slate-200 w-full"></div>
                        <span class="bg-white px-3 text-[11px] text-slate-400 font-medium uppercase absolute">Or</span>
                    </div>

                    <!-- Register Action -->
                    <div class="text-center pt-1">
                        <p class="text-xs text-slate-500 mb-3">Don't have an account yet?</p>
                        <button
                            wire:click="switchToRegister"
                            type="button"
                            class="w-full py-3 bg-[#D4A017]/15 hover:bg-[#D4A017]/25 text-[#0B2A5B] border border-[#D4A017]/40 font-extrabold text-xs rounded-xl transition duration-200">
                            Create New Membership Account
                        </button>
                    </div>

                </form>

            </div>

        </div>
    @endif

    <!-- SweetAlert Listeners Script -->
    @script
    <script>
        $wire.on('swal:loading', () => {
            Swal.fire({
                title: 'Signing In...',
                text: 'Please wait while we verify your account.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        $wire.on('swal:close', () => {
            Swal.close();
        });

        $wire.on('swal:success', (event) => {
            const message = event.message || event[0]?.message || 'Login successful!';

            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                Livewire.navigate('{{ route('home') }}');
            });
        });
    </script>
    @endscript
</div>
