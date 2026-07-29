<div class="branches-container">

    <div class="members-header">
        <h2 class="masterlist-title">
            Branches
        </h2>

        <button
            wire:click="openCreateModal"
            class="masterlist-btn bg-[#0C2C6B] text-white px-4 py-2 rounded-lg hover:bg-blue-600 cursor-pointer">
            Add Branch
        </button>
    </div>

    <p class="masterlist-description">
        Branch management content and directory.
    </p>

    <!-- Success Message Notification -->
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- BRANCHES TABLE -->
    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Branch Code</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Branch Name</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Address</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($branches as $index => $branch)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                            {{ $branch->branchCode }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                            {{ $branch->branchname }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                {{ $branch->type }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            {{ $branch->address ?? 'No address provided.' }}
                        </td>

                        <!-- Actions Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2 justify-center">
                                {{-- EDIT BUTTON --}}
                                <button
                                    wire:click="editBranch({{ $branch->BranchID }})"
                                    title="Update Branch"
                                    class="w-10 h-10 flex items-center justify-center rounded bg-[#0C2C6B] text-white hover:bg-blue-600 cursor-pointer">
                                    ✏️
                                </button>

                                {{-- DELETE BUTTON --}}
                                <button
                                    wire:click="confirmDeleteBranch({{ $branch->BranchID }})"
                                    title="Delete Branch"
                                    class="w-10 h-10 flex items-center justify-center rounded bg-red-500 text-white hover:bg-red-600 text-sm font-semibold cursor-pointer">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No active branches available at the moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- ADD/EDIT BRANCH MODAL -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm bg-opacity-50">
            <div class="w-full max-w-lg p-6 bg-white rounded-lg shadow-xl">
                <h3 class="mb-4 text-lg font-bold">
                    {{ $branch_id ? 'Edit Branch' : 'Add New Branch' }}
                </h3>

                <form wire:submit.prevent="saveBranch">
                    <div class="grid grid-cols-1 gap-4">

                        <!-- Branch Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Branch Code</label>
                            <input type="text" wire:model="branchCode" class="w-full p-2 mt-1 border rounded-lg">
                            @error('branchCode') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <!-- Branch Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Branch Name</label>
                            <input type="text" wire:model="branchname" class="w-full p-2 mt-1 border rounded-lg">
                            @error('branchname') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <input type="text" wire:model="type" class="w-full p-2 mt-1 border rounded-lg" placeholder="e.g., Main, Satellite">
                            @error('type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea wire:model="address" class="w-full p-2 mt-1 border rounded-lg" rows="3"></textarea>
                            @error('address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition cursor-pointer">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-white bg-[#0C2C6B] rounded-lg hover:bg-blue-700 transition cursor-pointer">
                            Save Branch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @script
    <script>
        $wire.on('confirm-delete', (event) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteBranch(event.id);
                }
            });
        });

        $wire.on('branch-deleted', () => {
            Swal.fire({
                title: 'Deleted!',
                text: 'Branch has been successfully deleted.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        });
    </script>
    @endscript

</div>
