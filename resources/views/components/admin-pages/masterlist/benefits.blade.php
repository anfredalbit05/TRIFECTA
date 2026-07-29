<div class="benefits-container">

    <div class="members-header">
        <h2 class="masterlist-title">
            Benefits
        </h2>

        <button
            wire:click="openCreateModal"
            class="masterlist-btn bg-[#0C2C6B] text-white px-4 py-2 rounded-lg hover:bg-blue-600 cursor-pointer">
            Add Benefit
        </button>
    </div>

    <p class="masterlist-description">
        Available member incentives and rewards.
    </p>

    <!-- Success Message Notification -->
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- BENEFITS TABLE -->
    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Benefit Name</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Details</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Holder Type</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($benefits as $index => $benefit)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                            {{ $benefit->name }}
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            {{ $benefit->details ?? 'No details provided.' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                {{ $benefit->holdertype }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            ₱{{ number_format($benefit->amount, 2) }}
                        </td>

                        <!-- Actions Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2 justify-center">
                                {{-- EDIT BUTTON --}}
                                <button
                                    wire:click="editBenefit({{ $benefit->BentID }})"
                                    title="Update Benefit"
                                    class="w-10 h-10 flex items-center justify-center rounded bg-[#0C2C6B] text-white hover:bg-blue-600 cursor-pointer">
                                    ✏️
                                </button>

                                {{-- DELETE BUTTON --}}
                                <button
                                    wire:click="confirmDeleteBenefit({{ $benefit->BentID }})"
                                    title="Delete Benefit"
                                    class="w-10 h-10 flex items-center justify-center rounded bg-red-500 text-white hover:bg-red-600 text-sm font-semibold cursor-pointer">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No active benefits available at the moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- ADD/EDIT BENEFIT MODAL -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm bg-opacity-50">
            <div class="w-full max-w-lg p-6 bg-white rounded-lg shadow-xl">
                <h3 class="mb-4 text-lg font-bold">
                    {{ $benefit_id ? 'Edit Benefit' : 'Add New Benefit' }}
                </h3>

                <form wire:submit.prevent="saveBenefit">
                    <div class="grid grid-cols-1 gap-4">

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Benefit Name</label>
                            <input type="text" wire:model="name" class="w-full p-2 mt-1 border rounded-lg">
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount (₱)</label>
                            <input type="number" step="0.01" wire:model="amount" class="w-full p-2 mt-1 border rounded-lg">
                            @error('amount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <!-- Holder Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Holder Type</label>
                            <select wire:model="holdertype" class="w-full p-2 mt-1 border rounded-lg">
                                <option value="Principal">Principal</option>
                                <option value="Dependent">Dependent</option>
                                <option value="Both">Both</option>
                            </select>
                            @error('holdertype') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <!-- Details -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Details</label>
                            <textarea wire:model="details" class="w-full p-2 mt-1 border rounded-lg" rows="3"></textarea>
                            @error('details') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
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
                            Save Benefit
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
                    $wire.deleteBenefit(event.id);
                }
            });
        });

        $wire.on('benefit-deleted', () => {
            Swal.fire({
                title: 'Deleted!',
                text: 'Benefit has been successfully deleted.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        });
    </script>
    @endscript

</div>
