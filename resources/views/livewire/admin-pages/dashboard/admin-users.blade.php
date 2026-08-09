<div class="w-full px-4 sm:px-6 lg:px-8 py-6">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
        <h2 class="text-2xl font-bold text-slate-800">
            All Users
        </h2>
    </div>

    <p class="text-sm text-slate-500 mb-6">
        List of all registered users.
    </p>

    <!-- FLASH MESSAGE -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-xl border border-green-200">
            {{ session('message') }}
        </div>
    @endif

    <!-- SEARCH INPUT & REFRESH BUTTON CONTAINER -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <!-- SEARCH -->
        <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                🔍
            </span>
            <input type="text" wire:model.live="search" placeholder="Search users..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
        </div>

        <!-- REFRESH BUTTON -->
        <button wire:click="resetTable" wire:loading.attr="disabled" title="Refresh Table"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
            <span wire:loading.remove wire:target="resetTable">🔄</span>
            <span wire:loading wire:target="resetTable" class="animate-spin">⏳</span>
            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#0C2C6B] text-white text-xs uppercase">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Username</th>
                    <th class="py-3 px-4">Contact</th>
                    <th class="py-3 px-4">Gender</th>
                    <th class="py-3 px-4">Birthday</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Role ID</th>
                    <th class="py-3 px-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 px-4">
                            {{ $users->firstItem() + $index }}
                        </td>
                        <td class="py-4 px-4 font-semibold">
                            {{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }}
                        </td>
                        <td class="py-4 px-4">{{ $user->username }}</td>
                        <td class="py-4 px-4">{{ $user->contactno ?? '-' }}</td>
                        <td class="py-4 px-4">{{ $user->gender ?? '-' }}</td>
                        <td class="py-4 px-4">
                            {{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('F d, Y') : '-' }}
                        </td>
                        <td class="py-4 px-4">
                            @if (strtolower($user->status) === 'active')
                                <span class="px-3 py-1 rounded-md bg-[#0C2C6B] text-white text-xs">Active</span>
                            @else
                                <span
                                    class="px-3 py-1 rounded-md border border-amber-400 bg-amber-500 text-white text-xs">Inactive</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">{{ $user->RoleID }}</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- User Access Modal Trigger Icon -->
                                <button wire:click="openAccessModal({{ $user->UserID }})" title="User Access"
                                    class="cursor-pointer p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </button>
                                <!-- View Icon -->
                                <button wire:click="viewUser({{ $user->UserID }})" title="View"
                                    class="cursor-pointer p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <!-- Edit Icon -->
                                <button wire:click="editUser({{ $user->UserID }})" title="Edit"
                                    class="cursor-pointer p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <!-- Delete Icon -->
                                <button wire:click="confirmDelete({{ $user->UserID }})" title="Delete"
                                    class="cursor-pointer p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="cursor-pointer text-center py-8 text-slate-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>

   <!-- VIEW USER MODAL -->
    @if ($isViewModalOpen && $this->selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden relative border border-slate-100">
                <!-- Modal Header -->
                <div class="bg-[#0C2C6B] px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <h3 class="text-base font-bold uppercase tracking-wider">User Details</h3>
                    </div>
                    <button wire:click="$set('isViewModalOpen', false)"
                        class="cursor-pointer text-white/80 hover:text-white text-xl font-bold transition">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                    <!-- Profile Header Card -->
                    <div class="flex items-center space-x-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        @if(!empty($this->selectedUser->profilepic))
                            <img src="{{ asset($this->selectedUser->profilepic) }}" alt="Profile Picture" class="w-14 h-14 rounded-full object-cover border border-slate-200 shadow-sm shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-full bg-[#0C2C6B] text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                                {{ strtoupper(substr($this->selectedUser->firstname, 0, 1) . substr($this->selectedUser->lastname, 0, 1)) }}
                            </div>
                        @endif
                        <div class="overflow-hidden">
                            <h4 class="text-base font-bold text-slate-800 truncate">
                                {{ $this->selectedUser->firstname }} {{ $this->selectedUser->middlename }} {{ $this->selectedUser->lastname }}
                            </h4>
                            <p class="text-xs text-slate-500">@<span>{{ $this->selectedUser->username }}</span></p>
                            <p class="text-xs text-slate-400 truncate">{{ $this->selectedUser->email ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Contact No</span>
                            <span class="font-medium text-slate-700">{{ $this->selectedUser->contactno ?? '-' }}</span>
                        </div>

                        <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Gender</span>
                            <span class="font-medium text-slate-700">{{ $this->selectedUser->gender ?? '-' }}</span>
                        </div>

                        <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Birthdate</span>
                            <span class="font-medium text-slate-700">
                                {{ $this->selectedUser->birthdate ? \Carbon\Carbon::parse($this->selectedUser->birthdate)->format('F d, Y') : '-' }}
                            </span>
                        </div>

                        <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Role ID</span>
                            <span class="font-medium text-slate-700">{{ $this->selectedUser->RoleID }}</span>
                        </div>

                        <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100 sm:col-span-2">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Address</span>
                            <span class="font-medium text-slate-700">{{ $this->selectedUser->address ?? '-' }}</span>
                        </div>

                        <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Referral Code</span>
                            <span class="font-medium font-mono text-slate-700">{{ $this->selectedUser->referralCode ?? '-' }}</span>
                        </div>

                        <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Upline Code</span>
                            <span class="font-medium font-mono text-slate-700">{{ $this->selectedUser->uplineCode ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Status Row -->
                    <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Account Status</span>
                        @if (strtolower($this->selectedUser->status) === 'active')
                            <span class="px-3 py-1 rounded-md bg-[#0C2C6B] text-white text-xs font-semibold">Active</span>
                        @else
                            <span class="px-3 py-1 rounded-md border border-amber-400 bg-amber-500 text-white text-xs font-semibold">Inactive</span>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-end">
                    <button wire:click="$set('isViewModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-300 transition">Close</button>
                </div>
            </div>
        </div>
    @endif

   <!-- EDIT USER MODAL -->
    @if ($isEditModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden relative border border-slate-100">
                <!-- Modal Header -->
                <div class="bg-[#0C2C6B] px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <h3 class="text-base font-bold uppercase tracking-wider">Edit User Details</h3>
                    </div>
                    <button wire:click="$set('isEditModalOpen', false)"
                        class="cursor-pointer text-white/80 hover:text-white text-xl font-bold transition">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">First Name</label>
                            <input type="text" wire:model="firstname"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                        </div>

                        <!-- Middle Name -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Middle Name</label>
                            <input type="text" wire:model="middlename"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Last Name</label>
                            <input type="text" wire:model="lastname"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Username</label>
                            <input type="text" wire:model="username"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                        </div>

                        <!-- Contact No -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Contact No</label>
                            <input type="text" wire:model="contactno"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Gender</label>
                            <select wire:model="gender"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm cursor-pointer">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Birthdate -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Birthdate</label>
                            <input type="date" wire:model="birthdate"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                        </div>

                        <!-- Role ID -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Role ID</label>
                            <input type="text" wire:model="RoleID"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Address</label>
                            <input type="text" wire:model="address"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                        </div>

                        <!-- Status -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Status</label>
                            <select wire:model="status"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm cursor-pointer">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-end space-x-2">
                    <button wire:click="$set('isEditModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-300 transition">Cancel</button>
                    <button wire:click="updateUser"
                        class="cursor-pointer px-4 py-2 bg-[#0C2C6B] text-white rounded-xl text-sm font-semibold hover:bg-[#092254] transition shadow-sm">Save Changes</button>
                </div>
            </div>
        </div>
    @endif

   <!-- USER ACCESS MODAL -->
    @if ($isAccessModalOpen && $this->selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden relative border border-slate-100 max-h-[90vh] flex flex-col">
                <!-- Modal Header -->
                <div class="bg-[#0C2C6B] px-6 py-4 flex items-center justify-between text-white shrink-0">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-base font-bold uppercase tracking-wider">User Access Control</h3>
                    </div>
                    <button wire:click="$set('isAccessModalOpen', false)"
                        class="cursor-pointer text-white/80 hover:text-white text-xl font-bold transition">&times;</button>
                </div>

                <!-- User Info Sub-header Card -->
                <div class="bg-slate-50 px-6 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-[#0C2C6B] text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                            {{ strtoupper(substr($this->selectedUser->firstname, 0, 1) . substr($this->selectedUser->lastname, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">
                                {{ $this->selectedUser->firstname }} {{ $this->selectedUser->middlename }} {{ $this->selectedUser->lastname }}
                            </h4>
                            <p class="text-xs text-slate-500">@<span>{{ $this->selectedUser->username }}</span></p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 shadow-sm self-start sm:self-auto">
                        <span class="text-slate-400 uppercase tracking-wide text-[10px]">Role ID:</span>
                        <span>{{ $this->selectedUser->RoleID }}</span>
                    </div>
                </div>

                <!-- Modal Body (Table Container) -->
                <div class="overflow-y-auto flex-1 p-6">
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-100 text-xs text-slate-600 uppercase border-b border-slate-200 font-semibold tracking-wider">
                                    <th class="py-3 px-4">Module</th>
                                    <th class="py-3 px-3 text-center">⊕ Create</th>
                                    <th class="py-3 px-3 text-center">📥 Retrieve</th>
                                    <th class="py-3 px-3 text-center">✏️ Update</th>
                                    <th class="py-3 px-3 text-center">🗑️ Delete</th>
                                    <th class="py-3 px-3 text-center">🏷️ Status</th>
                                    <th class="py-3 px-3 text-center">🖨️ PrintAll</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($userAccess as $moduleName => $permissions)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 px-4 font-medium text-slate-700 flex items-center space-x-2.5">
                                            <input type="checkbox"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                            <span>{{ $moduleName }}</span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.create"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.retrieve"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.update"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.delete"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.status"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.printall"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-end space-x-2 shrink-0">
                    <button wire:click="$set('isAccessModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-300 transition">Close</button>
                    <button wire:click="saveAccess"
                        class="cursor-pointer px-4 py-2 bg-[#0C2C6B] text-white rounded-xl text-sm font-semibold transition shadow-sm">Save Changes</button>
                </div>
            </div>
        </div>
    @endif

 <!-- DELETE CONFIRMATION MODAL -->
    @if ($isDeleteModalOpen && $this->selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden relative border border-slate-100">
                <!-- Close Button -->
                <button wire:click="$set('isDeleteModalOpen', false)"
                    class="absolute top-4 right-4 cursor-pointer text-slate-400 hover:text-slate-600 text-xl font-bold transition z-10">&times;</button>

                <!-- Modal Body -->
                <div class="p-6 pt-8 text-center space-y-4">
                    <!-- Warning Icon Badge -->
                    <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mx-auto shadow-sm border border-rose-100 rotate-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>

                    <!-- Title & Description -->
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-slate-800">Delete User Account</h3>
                        <p class="text-sm text-slate-500 leading-relaxed px-2">
                            Are you sure you want to delete <strong class="text-slate-700 font-semibold">{{ $this->selectedUser->firstname }} {{ $this->selectedUser->lastname }}</strong>? This action cannot be undone.
                        </p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button wire:click="$set('isDeleteModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-300 transition">Cancel</button>
                    <button wire:click="deleteUser"
                        class="cursor-pointer px-4 py-2 bg-rose-600 text-white rounded-xl text-sm font-semibold hover:bg-rose-700 transition shadow-sm">Yes, Delete</button>
                </div>
            </div>
        </div>
    @endif

</div>
