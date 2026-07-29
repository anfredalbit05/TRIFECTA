<div class="members-container">

    <div class="members-header">
        <h2 class="masterlist-title">
            Members
        </h2>

        <button class="masterlist-btn">
            Add Member
        </button>
    </div>

    <div class="masterlist-search">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search members...">
    </div>

    <div class="member-table">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Gender</th>
                    <th>Birthday</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($members as $member)
                    <tr>
                        <td>
                            {{ $member->firstname }}
                            {{ $member->middlename }}
                            {{ $member->lastname }}
                        </td>

                        <td>
                            {{ $member->contactno }}
                        </td>

                        <td>
                            {{ $member->gender }}
                        </td>

                        <td>
                            {{ $member->birthdate ? \Carbon\Carbon::parse($member->birthdate)->format('F d Y') : '-' }}
                        </td>

                        <td>
                            @if(strtolower($member->status) === 'active')
                                <span class="inline-flex justify-center items-center w-24 px-3 py-1 rounded-md bg-[#0C2C6B] text-white text-sm font-semibold">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex justify-center items-center w-24 px-3 py-1 rounded-md bg-red-500 text-white text-sm font-semibold">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="flex gap-2 justify-center">
                                {{-- UPDATE --}}
                                <button
                                    wire:click="editMember({{ $member->UserID }})"
                                    title="Update Member"
                                    class="w-10 h-10 flex items-center justify-center rounded bg-[#0C2C6B] text-white hover:bg-blue-600 cursor-pointer">
                                    ✏️
                                </button>

                                {{-- STATUS / ACTIVATION --}}
                                @if(strtolower($member->status) === 'active')
                                    <button
                                        wire:click="confirmStatus({{ $member->UserID }})"
                                        title="Deactivate Member"
                                        class="w-24 h-10 flex items-center justify-center rounded bg-red-500 text-white hover:bg-red-600 text-sm font-semibold cursor-pointer">
                                        Deactivate
                                    </button>
                                @elseif($member->has_pending_activation)
                                    <button
                                        wire:click="openReview({{ $member->UserID }})"
                                        title="Review Activation"
                                        class="w-24 h-10 flex items-center justify-center rounded bg-amber-500 text-white hover:bg-amber-600 text-sm font-semibold cursor-pointer">
                                        Pending
                                    </button>
                                @else
                                    <button
                                        wire:click="openActivation({{ $member->UserID }})"
                                        title="Activate Member"
                                        class="w-24 h-10 flex items-center justify-center rounded bg-[#0C2C6B] text-white hover:bg-blue-600 text-sm font-semibold cursor-pointer">
                                        Activate
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6">
                            No members found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <livewire:member-pages.my-profile.activation-modal />

@script
<script>
    $wire.on('confirm-status', (data) => {
        const actionText = data.action === 'deactivate'
            ? "I'm sure to deactivate"
            : "I'm sure to activate";

        Swal.fire({
            title: 'Are you sure?',
            text: 'Type the confirmation text below to continue.',
            icon: 'warning',
            input: 'text',
            inputPlaceholder: actionText,
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonColor: '#0C2C6B',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Confirm ' + data.action,
            cancelButtonText: 'Cancel',
            preConfirm: (value) => {
                if (value !== actionText) {
                    Swal.showValidationMessage('Please type: ' + actionText);
                    return false;
                }
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $wire.updateStatus(data.id);
            }
        });
    });

    $wire.on('status-updated', () => {
        Swal.fire({
            title: 'Updated!',
            text: 'Member status has been updated.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    });
</script>
@endscript

</div>
