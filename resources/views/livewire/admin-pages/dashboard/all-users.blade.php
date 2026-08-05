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
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative">
                <h3 class="text-lg font-bold text-slate-800 mb-4">User Details</h3>
                <div class="space-y-3 text-sm text-slate-600">
                    <p><strong>Full Name:</strong> {{ $this->selectedUser->firstname }}
                        {{ $this->selectedUser->middlename }} {{ $this->selectedUser->lastname }}</p>
                    <p><strong>Username:</strong> {{ $this->selectedUser->username }}</p>
                    <p><strong>Contact No:</strong> {{ $this->selectedUser->contactno ?? '-' }}</p>
                    <p><strong>Gender:</strong> {{ $this->selectedUser->gender ?? '-' }}</p>
                    <p><strong>Birthdate:</strong>
                        {{ $this->selectedUser->birthdate ? \Carbon\Carbon::parse($this->selectedUser->birthdate)->format('F d, Y') : '-' }}
                    </p>
                    <p><strong>Status:</strong> {{ $this->selectedUser->status }}</p>
                    <p><strong>Role ID:</strong> {{ $this->selectedUser->RoleID }}</p>
                </div>
                <div class="mt-6 flex justify-end">
                    <button wire:click="$set('isViewModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300">Close</button>
                </div>
            </div>
        </div>
    @endif

    <!-- EDIT USER MODAL -->
    @if ($isEditModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Edit User</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">First Name</label>
                        <input type="text" wire:model="firstname"
                            class="w-full border border-slate-200 rounded-lg p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Middle Name</label>
                        <input type="text" wire:model="middlename"
                            class="w-full border border-slate-200 rounded-lg p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Last Name</label>
                        <input type="text" wire:model="lastname"
                            class="w-full border border-slate-200 rounded-lg p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Username</label>
                        <input type="text" wire:model="username"
                            class="w-full border border-slate-200 rounded-lg p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select wire:model="status"
                            class="w-full border border-slate-200 rounded-lg p-2 text-sm cursor-pointer">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <button wire:click="$set('isEditModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium">Cancel</button>
                    <button wire:click="updateUser"
                        class="cursor-pointer px-4 py-2 bg-[#0C2C6B] text-white rounded-lg text-sm font-medium">Save
                        Changes</button>
                </div>
            </div>
        </div>
    @endif

    <!-- USER ACCESS MODAL -->
    @if ($isAccessModalOpen && $this->selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full p-6 relative max-h-[90vh] flex flex-col">
                <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 uppercase tracking-wide">
                        USER ACCESS - <span class="font-normal text-slate-600">Role ID:
                            {{ $this->selectedUser->RoleID }}</span> <br>
                        NAME: <span class="font-normal text-slate-600">{{ $this->selectedUser->firstname }}
                            {{ $this->selectedUser->middlename }} {{ $this->selectedUser->lastname }}</span>
                    </h3>
                    <button wire:click="$set('isAccessModalOpen', false)"
                        class="cursor-pointer text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <div class="overflow-y-auto flex-1 my-4 pr-2">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="text-xs text-slate-500 uppercase border-b border-slate-200">
                                <th class="py-2">Module</th>
                                <th class="py-2 text-center">⊕ Create</th>
                                <th class="py-2 text-center">📥 Retrieve</th>
                                <th class="py-2 text-center">✏️ Update</th>
                                <th class="py-2 text-center">🗑️ Delete</th>
                                <th class="py-2 text-center">🏷️ Status</th>
                                <th class="py-2 text-center">🖨️ PrintAll</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($userAccess as $moduleName => $permissions)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 font-medium text-slate-700 flex items-center space-x-2">
                                        <input type="checkbox"
                                            class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B]">
                                        <span>{{ $moduleName }}</span>
                                    </td>
                                    <td class="py-3 text-center"><input type="checkbox"
                                            wire:model="userAccess.{{ $moduleName }}.create"
                                            class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B]">
                                    </td>
                                    <td class="py-3 text-center"><input type="checkbox"
                                            wire:model="userAccess.{{ $moduleName }}.retrieve"
                                            class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B]">
                                    </td>
                                    <td class="py-3 text-center"><input type="checkbox"
                                            wire:model="userAccess.{{ $moduleName }}.update"
                                            class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B]">
                                    </td>
                                    <td class="py-3 text-center"><input type="checkbox"
                                            wire:model="userAccess.{{ $moduleName }}.delete"
                                            class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B]">
                                    </td>
                                    <td class="py-3 text-center"><input type="checkbox"
                                            wire:model="userAccess.{{ $moduleName }}.status"
                                            class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B]">
                                    </td>
                                    <td class="py-3 text-center"><input type="checkbox"
                                            wire:model="userAccess.{{ $moduleName }}.printall"
                                            class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B]">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end space-x-2">
                    <button wire:click="saveAccess"
                        class="cursor-pointer px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">Save</button>
                    <button wire:click="$set('isAccessModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300">Close</button>
                </div>
            </div>
        </div>
    @endif

    <!-- DELETE CONFIRMATION MODAL -->
    @if ($isDeleteModalOpen && $this->selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6 text-center">
                <h3 class="text-lg font-bold text-slate-800 mb-2">Delete User</h3>
                <p class="text-sm text-slate-500 mb-6">Are you sure you want to delete
                    <strong>{{ $this->selectedUser->firstname }} {{ $this->selectedUser->lastname }}</strong>?</p>
                <div class="flex justify-center space-x-2">
                    <button wire:click="$set('isDeleteModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium">Cancel</button>
                    <button wire:click="deleteUser"
                        class="cursor-pointer px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-medium hover:bg-rose-700">Delete</button>
                </div>
            </div>
        </div>
    @endif

</div>
