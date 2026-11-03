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
                    <h2 class="text-xl sm:text-2xl font-black">
                        {{ $isForgotPassword ? 'Reset Password' : 'Welcome Back' }}
                    </h2>
                    <p class="text-xs text-slate-300 mt-1">
                        {{ $isForgotPassword ? 'Enter your email to receive a password reset link' : 'Access your TRUELIFE member portal' }}
                    </p>
                </div>

                <!-- Session Status (e.g. "Your password has been reset!") -->
                @if (session('status'))
                    @if (session('password_reset_success'))
                        <div class="mx-6 sm:mx-8 mt-5 px-4 py-4 rounded-xl bg-emerald-50 border border-emerald-200">
                            <p class="text-xs font-extrabold uppercase tracking-wide text-emerald-700">
                                ✅ Password Reset Successful!
                            </p>
                            <p class="text-xs text-emerald-700 mt-1 leading-relaxed">
                                {{ session('status') }}
                            </p>
                        </div>
                    @else
                        <div class="mx-6 sm:mx-8 mt-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                            ✅ {{ session('status') }}
                        </div>
                    @endif
                @endif

                <!-- Form Body -->
                @if(!$isForgotPassword)
                    <!-- ================= LOGIN FORM ================= -->
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

                        <!-- Password Input with Toggle -->
                        <div x-data="{ showPassword: false }">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-bold text-slate-700">Password</label>
                            </div>
                            <div class="relative">
                                <input
                                    wire:model="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    class="w-full border border-slate-300 rounded-xl px-4 py-3 pr-10 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:border-[#0B2A5B] focus:outline-none transition">

                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                    <!-- Eye Icon (Show) -->
                                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <!-- Eye Slash Icon (Hide) -->
                                    <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
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

                            <!-- Forgot Password -->
                            <button
                                type="button"
                                wire:click="toggleForgotPassword"
                                class="text-xs font-bold text-[#0B2A5B] hover:text-[#D4A017] hover:underline transition cursor-pointer">
                                Forgot password?
                            </button>
                        </div>

                        <!-- Submit Login Button -->
                        <button
                            type="submit"
                            onclick="Swal.fire({ title: 'Please wait...', text: 'Processing your request.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); } })"
                            class="w-full py-3.5 bg-[#0B2A5B] hover:bg-[#071D3F] text-white font-bold text-sm rounded-xl shadow-lg transition duration-200 mt-2 flex items-center justify-center cursor-pointer">
                            <span>Sign In</span>
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
                @else
                    <!-- ================= FORGOT PASSWORD FORM ================= -->
                    <form wire:submit.prevent="sendPasswordResetLink" class="p-6 sm:p-8 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Account Email Address</label>
                            <input
                                wire:model="forgotEmail"
                                type="email"
                                placeholder="name@example.com"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:border-[#0B2A5B] focus:outline-none transition">
                            @error('forgotEmail') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            onclick="Swal.fire({ title: 'Please wait...', text: 'Processing your request.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); } })"
                            class="w-full py-3.5 bg-[#0B2A5B] hover:bg-[#071D3F] text-white font-bold text-sm rounded-xl shadow-lg transition duration-200 mt-2 flex items-center justify-center cursor-pointer">
                            <span>Send Reset Link</span>
                        </button>

                        <!-- Back to Login Action -->
                        <div class="text-center pt-2">
                            <button
                                wire:click="toggleForgotPassword"
                                type="button"
                                class="text-xs text-slate-600 hover:text-[#0B2A5B] font-bold transition">
                                ← Back to Sign In
                            </button>
                        </div>
                    </form>
                @endif

            </div>

        </div>
    @endif

  <!-- SweetAlert Listeners Script -->
    @script
    <script>

        const SWAL_NAVY = '#0B2A5B';
        if (!window.hasSwalListeners) {
            window.hasSwalListeners = true;

            const style = document.createElement('style');
            style.innerHTML = '.swal2-container { z-index: 999999 !important; }';
            document.head.appendChild(style);

            {{-- swal:close is handled globally by <x-shared.sweet-alert-handler /> --}}

            {{-- swal:error was dead code here (LoginModal never dispatches it) —
                 removed; global handler covers it if it ever fires. --}}

            $wire.on('password-reset-link-sent', (event) => {
                Swal.close();
                const data = Array.isArray(event) ? event[0] : event;
                const message = data?.message || 'Check your email for the reset link!';

                Swal.fire({
                    icon: 'success',
                    title: 'Reset Link Sent!',
                    text: message,
                    confirmButtonColor: '#0B2A5B',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                }).then(() => {
                    // Return to the sign-in form so the member can log in
                    // with their new password right away.
                    $wire.resetToLogin();
                });
            });

                Livewire.on('swal:warning', (event) => {
                Swal.fire({
                    icon: 'warning',
                    title: event.message,
                    text: 'Please try again later',
                    confirmButtonColor: SWAL_NAVY,
                    confirmButtonText: 'Okay'
                });
            });

            $wire.on('swal:successed', (event) => {
                Swal.close();
                // Properly extract the message sent from Livewire 3 dispatch
                const data = Array.isArray(event) ? event[0] : event;
                const message = data?.message || 'Login successful!';

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: message,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0B2A5B',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.navigate('{{ route('home') }}');
                    }
                });
            });


        }
    </script>
    @endscript
</div>
