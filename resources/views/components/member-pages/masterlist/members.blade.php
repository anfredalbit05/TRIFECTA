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

                    <th>
                        Name
                    </th>

                    <th>
                        Member ID
                    </th>

                    <th>
                        Status
                    </th>

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
                            UID-{{ $member->id }}
                        </td>

                        <td>
                            {{ $member->status }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3" class="text-center py-6">
                            No members found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>
