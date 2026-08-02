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

    <!-- SEARCH -->
    <div class="relative max-w-sm mb-6">
        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
            🔍
        </span>

        <input
            type="text"
            wire:model.live="search"
            placeholder="Search users..."
            class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
        >
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
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 text-sm">

                @forelse($users as $index => $user)

                    <tr class="hover:bg-slate-50">

                        <td class="py-4 px-4">
                            {{ $users->firstItem() + $index }}
                        </td>

                        <td class="py-4 px-4 font-semibold">
                            {{ $user->firstname }}
                            {{ $user->middlename }}
                            {{ $user->lastname }}
                        </td>

                        <td class="py-4 px-4">
                            {{ $user->username }}
                        </td>

                        <td class="py-4 px-4">
                            {{ $user->contactno }}
                        </td>

                        <td class="py-4 px-4">
                            {{ $user->gender }}
                        </td>

                        <td class="py-4 px-4">
                            {{ $user->birthdate
                                ? \Carbon\Carbon::parse($user->birthdate)->format('F d, Y')
                                : '-' }}
                        </td>

                        <td class="py-4 px-4">

                            @if(strtolower($user->status) === 'active')

                                <span class="px-3 py-1 rounded-md bg-[#0C2C6B] text-white text-xs">
                                    Active
                                </span>

                            @else

                             <span class="px-3 py-1 rounded-md border border-amber-400 bg-amber-500 text-white text-xs">
    Inactive
</span>

                            @endif

                        </td>

                        <td class="py-4 px-4">
                            {{ $user->RoleID }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center py-8 text-slate-500">
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

</div>
