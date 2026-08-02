<div>

    @if ($open)

        <!-- Backdrop -->
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm">

            <!-- Modal -->
            <div class="w-full max-w-6xl overflow-hidden rounded-2xl bg-white shadow-2xl">

                <!-- Header -->
                <div class="bg-[#0C2C6B] px-6 py-5 text-white">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-4">

                            <!-- Avatar -->
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 text-2xl font-bold">

                                {{ strtoupper(substr($first_name, 0, 1)) }}

                            </div>

                            <div>

                                <h2 class="text-2xl font-bold">

                                    {{ $first_name }}
                                    {{ $middle_name }}
                                    {{ $surname }}

                                </h2>

                                <p class="text-sm text-blue-100">

                                    Member Information

                                </p>

                            </div>

                        </div>

                        <button
                            wire:click="close"
                            class="rounded-lg bg-white/20 px-4 py-2 transition hover:bg-red-500">

                            ✕

                        </button>

                    </div>

                </div>

                <!-- Body -->
                <div class="max-h-[80vh] overflow-y-auto p-6">

                    <!-- Personal Information -->
                    <div class="mb-6 rounded-xl border border-slate-200 shadow-sm">

                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">

                            <h3 class="font-bold text-slate-800">

                                👤 Personal Information

                            </h3>

                        </div>

                        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 lg:grid-cols-3">

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Surname</p>
                                <p class="mt-1 font-semibold">{{ $surname }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">First Name</p>
                                <p class="mt-1 font-semibold">{{ $first_name }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Middle Name</p>
                                <p class="mt-1 font-semibold">{{ $middle_name ?: 'N/A' }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Gender</p>
                                <p class="mt-1 font-semibold">{{ $gender }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Civil Status</p>
                                <p class="mt-1 font-semibold">{{ $civil_status }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Birthdate</p>
                                <p class="mt-1 font-semibold">
                                    {{ \Carbon\Carbon::parse($birthdate)->format('F d, Y') }}
                                </p>
                            </div>

                        </div>

                    </div>

                    <!-- Address -->
                    <div class="mb-6 rounded-xl border border-slate-200 shadow-sm">

                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">

                            <h3 class="font-bold text-slate-800">

                                📍 Address

                            </h3>

                        </div>

                        <div class="p-5">

                            <div class="rounded-lg bg-slate-50 p-4">

                                {{ $address }}

                            </div>

                        </div>

                    </div>

                    <!-- Contact Information -->
                    <div class="mb-6 rounded-xl border border-slate-200 shadow-sm">

                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">

                            <h3 class="font-bold text-slate-800">

                                📞 Contact Information

                            </h3>

                        </div>

                        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-3">

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Mobile</p>
                                <p class="mt-1 font-semibold">{{ $mobile }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Email</p>
                                <p class="mt-1 font-semibold">{{ $email ?: 'N/A' }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Username</p>
                                <p class="mt-1 font-semibold">{{ $username }}</p>
                            </div>

                        </div>

                    </div>

                    <div class="mb-6 rounded-xl border border-slate-200 shadow-sm">

    <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">

        <h3 class="font-bold text-slate-800">

            ✅ Activation Information

        </h3>

    </div>

    <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2">

        <div class="rounded-lg bg-slate-50 p-4">

            <p class="text-xs uppercase text-slate-400">

                Activation Status

            </p>

            <p class="mt-1 font-semibold">

             @if ($activationStatus === 'Approved')

                <span class="text-green-600">Approved</span>

            @elseif ($activationStatus === 'Rejected')

                <span class="text-red-600">Rejected</span>

            @elseif (empty($activationStatus))

                <span class="text-gray-600">N/A</span>

            @else

                <span class="text-yellow-600">Pending</span>

            @endif

            </p>

        </div>

        <div class="rounded-lg bg-slate-50 p-4">

            <p class="text-xs uppercase text-slate-400">

                {{ $activationLabel ?? 'Date' }}

            </p>

            <p class="mt-1 font-semibold">

                {{ $activationDate ?? 'N/A' }}

            </p>

        </div>

    </div>

</div>

                    <!-- Beneficiaries -->
                    <div class="rounded-xl border border-slate-200 shadow-sm">

                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">

                            <h3 class="font-bold text-slate-800">

                                👨‍👩‍👧 Beneficiaries

                            </h3>

                        </div>

                        <div class="p-5">

                            @forelse ($beneficiaries as $index => $beneficiary)

                                <div class="mb-4 rounded-xl border border-slate-200 p-4">

                                    <div class="mb-4 flex items-center justify-between">

                                        <h4 class="font-bold text-slate-700">

                                            Beneficiary {{ $index + 1 }}

                                        </h4>

                                        <span class="rounded-md bg-[#0C2C6B] px-3 py-1 text-xs font-semibold text-white">

                                            {{ $beneficiary['relationship'] ?: 'N/A' }}

                                        </span>

                                    </div>

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                                        <div class="rounded-lg bg-slate-50 p-4">

                                            <p class="text-xs uppercase text-slate-400">

                                                Full Name

                                            </p>

                                            <p class="mt-1 font-semibold">

                                                {{ $beneficiary['fullname'] ?: 'N/A' }}

                                            </p>

                                        </div>

                                        <div class="rounded-lg bg-slate-50 p-4">

                                            <p class="text-xs uppercase text-slate-400">

                                                Birthdate

                                            </p>

                                            <p class="mt-1 font-semibold">

                                                {{ $beneficiary['birthdate'] ?: 'N/A' }}

                                            </p>

                                        </div>

                                        <div class="rounded-lg bg-slate-50 p-4">

                                            <p class="text-xs uppercase text-slate-400">

                                                Relationship

                                            </p>

                                            <p class="mt-1 font-semibold">

                                                {{ $beneficiary['relationship'] ?: 'N/A' }}

                                            </p>

                                        </div>

                                    </div>

                                </div>

                            @empty

                                <div class="rounded-lg border border-dashed border-slate-300 p-10 text-center text-slate-500">

                                    No beneficiaries found.

                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>
