<div wire:key="spin-wheel-modal" x-data="{
    showModal:false,
    rotation:0,
    spinning:false,
    revealed:false,
    async startSpin(){
        if(this.spinning)return;
        this.spinning=true;
        this.revealed=false;
        await $wire.spin();
        const win=$wire.lastWin;
        if(!win){this.spinning=false;return;}
        const total=Math.max(1,$wire.spinTotal??1);
        const index=$wire.spinIndex??0;
        const seg=360/total;
        const jitter=(Math.random()-0.5)*seg*0.6;
        let next=-((index+0.5)*seg+jitter);
        while(next<this.rotation+360*5)next+=360;
        this.rotation=next;
        setTimeout(()=>{
            this.spinning=false;
            this.revealed=true;
            $wire.announceWinner();
            setTimeout(()=>{
                $wire.refreshWheel().then(()=>{this.revealed=false;});
            },3400);
        },4700);
    }
}"
x-on:show-spin-wheel.window="showModal=true"
x-on:close-spin-wheel.window="showModal=false">

@if($open)
<div x-show="showModal" x-cloak x-transition.opacity
    class="fixed inset-0 z-[60] flex items-start justify-center overflow-y-auto bg-black/60 p-4 sm:items-center"
    @click.self="showModal=false">
    <div class="relative my-auto w-full max-w-[1600px] overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl">

        {{-- HEADER --}}
        <div class="flex flex-wrap items-start justify-between gap-3 bg-[#0C2C6B] px-6 py-5 text-white">
            <div>
                <h3 class="flex items-center gap-2 text-lg font-bold">
                    <span class="text-2xl">🎡</span>Spin the Wheel
                </h3>
                <p class="mt-1 text-xs text-white/70">Pangkabuhayan raffle · drawn every 12th of the month</p>
            </div>
            <div class="flex items-center gap-3">
                <label class="text-[10px] font-semibold uppercase tracking-wider text-white/60">Cycle</label>
                <select wire:change="selectCycle($event.target.value)"
                    class="rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold text-white focus:outline-none focus:ring-2 focus:ring-[#D79B10]">
                    @forelse($cycles as $cycle)
                        <option value="{{ $cycle['date'] }}" @selected($cycle['date']===$drawDate) class="text-slate-800">
                            {{ $cycle['label'] }} @if($cycle['is_current'])(current) @endif · ₱{{ number_format($cycle['collected'],2) }} @if($cycle['winners']>0)· {{ $cycle['winners'] }} won @endif
                        </option>
                    @empty
                        <option value="">No cycle has collected Pangkabuhayan yet</option>
                    @endforelse
                </select>
                <button type="button" wire:click="closeModal" @click="showModal=false"
                    class="rounded-lg p-1.5 text-white/70 transition hover:bg-white/10 hover:text-white" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- TOP 4 COLUMNS --}}
        <div class="grid gap-5 px-6 pt-6 xl:grid-cols-3">

            {{-- QUALIFIED MEMBERS --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-4">
                <div class="flex items-center justify-between gap-2">
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-500">🎟️ Qualified members</h4>
                    <span class="rounded-full bg-[#0C2C6B]/10 px-2 py-0.5 text-[10px] font-bold text-[#0C2C6B]">
                        {{ count($this->filteredQualified()) }} / {{ count($qualified) }}
                    </span>
                </div>
                <p class="mt-1 text-[10px] text-gray-400">
                    Uplines whose invited member(s) had a Pangkabuhayan share collected in the window.
                </p>
                <input type="text" wire:model.live.debounce.300ms="memberSearch"
                    placeholder="Filter by name or referral code…"
                    class="mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-[11px] text-slate-800 focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">

                @if(count($qualified)===0)
                    <p class="mt-3 text-[11px] text-gray-400">No members qualified for this cycle yet.</p>
                @elseif(count($this->filteredQualified())===0)
                    <p class="mt-3 text-[11px] text-gray-400">No members match “{{ $memberSearch }}”.</p>
                @else
                    <ul class="mt-3 max-h-[390px] space-y-2 overflow-y-auto pr-1">
                        @foreach($this->filteredQualified() as $member)
                            <li class="flex items-start gap-2.5 rounded-xl border border-gray-100 bg-gray-50 px-3 py-2">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#0C2C6B] text-[10px] font-bold text-white"
                                    title="{{ $member['invites'] }} qualified invite(s) this cycle">
                                    {{ $member['invites'] }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold text-gray-800">{{ $member['name'] }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $member['code'] }} · {{ $member['invites'] }} invite(s)</p>
                                </div>
                                @if($member['wins']>0)
                                    <span class="shrink-0 rounded-full bg-[#D79B10]/10 px-2 py-0.5 text-[10px] font-bold text-[#D79B10]"
                                        title="Already drawn {{ $member['wins'] }} time(s) this cycle">
                                        🏆 {{ $member['wins'] }}×
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- SPIN THE WHEEL --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-4">
                @php
                    $total=count($wheel);
                    $cx=210;$cy=210;$r=196;
                    $seg=$total>0?360/$total:360;
                    $labelRadius=$r*0.72;
                    $showLabels=$total>0&&$total<=40;
                    $arc=$total>0?(2*M_PI*$labelRadius)/$total:0;
                    $fontSize=$total>0?min(11,max(5.5,$arc/3.6)):11;
                    $maxChars=$total>0?max(4,min(11,(int)floor($arc/($fontSize*0.6)))):8;
                    $palette=['#0C2C6B','#D79B10','#1E4E8C','#B8830D','#2E6DB4','#E0B23C','#123A7A','#8C6410'];
                @endphp

                <div class="relative mx-auto aspect-square w-full max-w-[420px]">
                    <div class="absolute left-1/2 top-[-10px] z-20 -translate-x-1/2">
                        <div class="h-0 w-0 border-x-[13px] border-x-transparent border-t-[26px] border-t-[#0C2C6B] drop-shadow-md"></div>
                    </div>

                    <div class="h-full w-full"
                        :style="`transform:rotate(${rotation}deg);transition:${spinning?'transform 4.6s cubic-bezier(.12,.72,.16,1)':'none'}`">
                        <svg viewBox="0 0 420 420" class="h-full w-full drop-shadow-lg">
                            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="#F1F5F9"/>

                            @if($total===1)
                                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="{{ $palette[0] }}"/>
                            @elseif($total>1)
                                @foreach($wheel as $i=>$member)
                                    @php
                                        $start=-90+($i*$seg);
                                        $end=$start+$seg;
                                        $x0=$cx+($r*cos(deg2rad($start)));
                                        $y0=$cy+($r*sin(deg2rad($start)));
                                        $x1=$cx+($r*cos(deg2rad($end)));
                                        $y1=$cy+($r*sin(deg2rad($end)));
                                        $largeArc=$seg>180?1:0;
                                        $mid=($i+0.5)*$seg;
                                        $flip=$mid>90&&$mid<270?180:0;
                                    @endphp

                                    <path d="M {{ $cx }} {{ $cy }} L {{ round($x0,2) }} {{ round($y0,2) }} A {{ $r }} {{ $r }} 0 {{ $largeArc }} 1 {{ round($x1,2) }} {{ round($y1,2) }} Z"
                                        fill="{{ $palette[$i%count($palette)] }}" stroke="#ffffff" stroke-width="1.5"/>

                                    @if($showLabels)
                                        <text x="{{ $cx }}" y="{{ round($cy-$labelRadius,2) }}"
                                            transform="rotate({{ round($mid,2) }},{{ $cx }},{{ $cy }}) rotate({{ $flip }},{{ $cx }},{{ round($cy-$labelRadius,2) }})"
                                            text-anchor="middle" dominant-baseline="middle"
                                            font-size="{{ round($fontSize,2) }}" font-weight="600"
                                            fill="#ffffff" stroke="#00000066" stroke-width="1.2" paint-order="stroke">
                                            {{ \Illuminate\Support\Str::limit($member['short'],$maxChars,'…') }}
                                        </text>
                                    @endif
                                @endforeach
                            @endif

                            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="44" fill="#ffffff" stroke="#0C2C6B" stroke-width="4"/>
                            <text x="{{ $cx }}" y="{{ $cy-5 }}" text-anchor="middle" font-size="11" font-weight="700" fill="#0C2C6B">TRUE LIFE</text>
                            <text x="{{ $cx }}" y="{{ $cy+10 }}" text-anchor="middle" font-size="9" fill="#64748B">{{ $total }} on wheel</text>
                        </svg>
                    </div>

                    {{-- WINNER REVEAL --}}
                    <div x-show="revealed" x-cloak
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute inset-0 z-30 flex items-center justify-center rounded-full bg-[#0C2C6B]/90 px-8 text-center text-white">
                        <div>
                            <div class="text-3xl">🎉</div>
                            <p class="mt-1 text-[10px] font-bold uppercase tracking-[0.25em] text-[#E0B23C]">Winner</p>
                            <h4 class="mt-1 text-xl font-bold leading-tight" x-text="$wire.lastWin?.name??''"></h4>
                            <p class="mt-2 text-[11px] leading-relaxed text-white/80"
                                x-text="'Spin #'+($wire.lastWin?.spin_order??'')+' · '+($wire.lastWin?.prize_name??'')+' · ₱'+Number($wire.lastWin?.prize_amount??0).toFixed(2)">
                            </p>
                            <p class="mt-1 text-[11px] text-white/60"
                                x-text="'Invited '+($wire.lastWin?.invites??0)+' member(s) this cycle'"></p>
                            <p class="mt-2 text-[10px] font-semibold"
                                :class="$wire.lastWin?.emailed?'text-emerald-300':'text-amber-300'"
                                x-text="($wire.lastWin?.emailed?'📧 ':'⚠️ ')+($wire.lastWin?.notice??'')"></p>
                        </div>
                    </div>
                </div>

                {{-- SPIN BUTTON --}}
                <div class="mt-4">
                    @if($errorMessage)
                        <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    @if($total===0)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs font-medium text-gray-500">
                            No members are on this wheel yet.
                        </div>
                    @elseif(!$canSpin)
                        <div class="rounded-xl border border-[#0C2C6B]/15 bg-[#0C2C6B]/5 px-4 py-4 text-center text-xs font-medium text-[#0C2C6B]">
                            🎟️ The wheel is spun by the administrators on {{ $summary['draw_label']??'' }}.
                        </div>
                    @elseif(($summary['remaining_slots']??0)<=0)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-center text-xs font-semibold text-emerald-700">
                            ✅ All {{ $summary['winners_target']??0 }} winners for this cycle have been drawn.
                        </div>
                    @else
                        <button type="button" @click="startSpin()" :disabled="spinning"
                            class="w-full rounded-xl bg-[#D79B10] px-4 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#B8830D] focus:outline-none focus:ring-2 focus:ring-[#D79B10] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70">
                            <span x-show="!spinning">🎡 Spin for winner #{{ ($summary['winners_count']??0)+1 }}</span>
                            <span x-show="spinning" x-cloak>Spinning the wheel…</span>
                        </button>
                        <p class="mt-2 text-center text-[11px] text-gray-500">
                            {{ $summary['remaining_slots']??0 }} winner slot(s) left of {{ $summary['winners_target']??0 }} ·
                            ₱{{ number_format($summary['pangkabuhayan_total']??0,2) }} Pangkabuhayan ÷
                            ₱{{ number_format($summary['prize_amount']??0,2) }} prize
                        </p>
                    @endif
                </div>
            </div>


            {{-- CLAIM INSTRUCTIONS and --}} {{-- PRIZE & RULES --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-4">

                  {{-- PRIZE & RULES --}}
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Prize &amp; rules</h4>

                @if($canSpin&&!($summary['has_winners']??false))
                    <div class="mt-3 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600">Kilos per prize</label>
                                <input type="number" step="0.25" min="0.25"
                                    wire:model.live.debounce.300ms="prizeKilos"
                                    placeholder="5"
                                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-800 focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                                @error('prizeKilos')<p class="mt-1 text-[11px] font-medium text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600">₱ per kilo</label>
                                <input type="number" step="0.01" min="1"
                                    wire:model.live.debounce.300ms="prizePerKilo"
                                    placeholder="55.00"
                                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-800 focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                                @error('prizePerKilo')<p class="mt-1 text-[11px] font-medium text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <p class="rounded-lg bg-[#0C2C6B]/5 px-3 py-2 text-[11px] leading-relaxed text-[#0C2C6B]">
                            🏅 Prize: <strong>{{ $this->derivedPrizeName() }}</strong>
                            = {{ $this->derivedPrizeKilos() }} kls × ₱{{ number_format($this->derivedPerKilo(),2) }}
                            = <strong>₱{{ number_format($this->derivedAmount(),2) }}</strong>
                            <span class="block text-gray-500">
                                Winners = ₱{{ number_format($summary['pangkabuhayan_total']??0,2) }}
                                Pangkabuhayan ÷ ₱{{ number_format($this->derivedAmount(),2) }}
                                = {{ $this->derivedTarget() }} winners.
                            </span>
                        </p>

                        <button type="button" wire:click="savePrize" wire:loading.attr="disabled"
                            class="w-full rounded-lg bg-[#0C2C6B] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#123A7A] disabled:opacity-60">
                            Save prize &amp; recalculate
                        </button>

                        <label class="flex cursor-pointer items-start gap-2 rounded-lg bg-gray-50 p-2.5">
                            <input type="checkbox" wire:change="setRepeatMode($event.target.checked)" @checked($allowRepeat)
                                class="mt-0.5 h-4 w-4 rounded border-gray-300 text-[#0C2C6B] focus:ring-[#0C2C6B]">
                            <span class="text-[11px] leading-relaxed text-gray-600">
                                A member may win more than once
                                <span class="block text-gray-400">Turn this off to give every member at most one prize.</span>
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-start gap-2 rounded-lg bg-gray-50 p-2.5">
                            <input type="checkbox" wire:change="setMembersOnly($event.target.checked)" @checked($membersOnly)
                                class="mt-0.5 h-4 w-4 rounded border-gray-300 text-[#0C2C6B] focus:ring-[#0C2C6B]">
                            <span class="text-[11px] leading-relaxed text-gray-600">
                                Members only (RoleID 4)
                                <span class="block text-gray-400">Turn this off to also put staff, agency and executive accounts on the wheel.</span>
                            </span>
                        </label>
                    </div>
                @else
                    <p class="mt-3 rounded-lg bg-gray-50 px-3 py-3 text-[11px] text-gray-500">
                        Prize settings are locked after the first winner is drawn.
                    </p>
                @endif

                <hr>
                <br>
                 {{-- CLAIM INSTRUCTIONS --}}
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Prize claim instructions</h4>
                <p class="mt-1 text-[11px] leading-relaxed text-gray-400">
                    Included in the winner email and announcement post.
                </p>

                @if($canSpin)
                    <textarea wire:model="claimInstructions" rows="8"
                        class="mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-[11px] text-slate-800 focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"></textarea>
                    @error('claimInstructions')<p class="mt-1 text-[11px] font-medium text-red-600">{{ $message }}</p>@enderror

                    <button type="button" wire:click="saveClaimInstructions" wire:loading.attr="disabled"
                        class="mt-2 w-full rounded-lg bg-[#0C2C6B] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#123A7A] disabled:opacity-60">
                        Save claim instructions
                    </button>
                @else
                    <div class="mt-3 rounded-lg bg-gray-50 px-3 py-3 text-[11px] text-gray-500">
                        Claim instructions are managed by administrators.
                    </div>
                @endif
            </div>
        </div>

        {{-- SUMMARY --}}
        <div class="px-6 pt-5">
            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-500">
                            {{ $summary['draw_label']??'' }} draw
                        </h4>
                        <p class="mt-1 text-[11px] text-gray-500">Covering {{ $summary['period_label']??'' }}</p>
                    </div>
                    <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs">
                        <span><span class="text-gray-500">Pangkabuhayan:</span> <strong class="text-[#0C2C6B]">₱{{ number_format($summary['pangkabuhayan_total']??0,2) }}</strong></span>
                        <span><span class="text-gray-500">Prize:</span> <strong>₱{{ number_format($summary['prize_amount']??0,2) }}</strong></span>
                        <span><span class="text-gray-500">Winners:</span> <strong>{{ $summary['winners_count']??0 }} / {{ $summary['winners_target']??0 }}</strong></span>
                        <span><span class="text-gray-500">On wheel:</span> <strong>{{ $total }}</strong></span>
                    </div>
                </div>
                <p class="mt-3 border-t border-gray-200 pt-3 text-[11px] text-gray-500">
                    🗓️ Next draw <strong class="text-gray-700">{{ $summary['next_draw_label']??'' }}</strong>
                    (covering {{ $summary['next_period_label']??'' }}).
                </p>
            </div>
        </div>

        {{-- WINNERS FULL WIDTH --}}
        <div class="px-6 pb-6 pt-5">
            <div class="rounded-2xl border border-gray-100 bg-white p-4">
                <div class="flex items-center justify-between gap-2">
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-500">🏆 Winners</h4>
                    <span class="rounded-full bg-[#0C2C6B]/10 px-2 py-0.5 text-[10px] font-bold text-[#0C2C6B]">
                        {{ count($this->filteredWinners()) }} / {{ count($winners) }}
                    </span>
                </div>

                <div class="mt-3 grid gap-2 md:grid-cols-[minmax(0,1fr)_180px]">
                    <input type="text" wire:model.live.debounce.300ms="winnerNameFilter"
                        placeholder="Filter by name or referral code…"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-[11px] text-slate-800 focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                    <select wire:model.live="winnerStatus" title="Filter winners by claim status"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-[11px] font-semibold text-slate-800 focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                        <option value="all">All claims</option>
                        <option value="claimed">✅ Claimed</option>
                        <option value="pending">⏳ Pending claim</option>
                    </select>
                </div>

                @if(count($winners)===0)
                    <p class="mt-3 text-[11px] text-gray-400">No winners drawn for this cycle yet.</p>
                @elseif(count($this->filteredWinners())===0)
                    <p class="mt-3 text-[11px] text-gray-400">No winners match the current filters.</p>
                @else
                    <div class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($this->filteredWinners() as $winner)
                            <div class="flex items-start gap-2.5 rounded-xl border border-gray-100 bg-gray-50 px-3 py-3">
                                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#D79B10] text-[10px] font-bold text-white">
                                    {{ $winner['spin_order'] }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold text-gray-800">
                                        {{ $winner['name'] }}
                                        <span class="ml-1 text-[10px] font-medium text-gray-400">{{ $winner['code'] }}</span>
                                    </p>
                                    <p class="text-[10px] text-gray-500">
                                        {{ $winner['prize_name'] }} · ₱{{ number_format($winner['prize_amount'],2) }} · {{ $winner['invites'] }} invite(s)
                                    </p>
                                    <p class="text-[10px] text-gray-400">{{ $winner['drawn_at'] }}</p>

                                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                        @if(($winner['claim_status']??'Pending')==='Completed')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700">
                                                ✅ Claimed{{ $winner['claimed_at']?' · '.$winner['claimed_at']:'' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-1.5 py-0.5 text-[10px] font-bold text-amber-700">
                                                ⏳ Pending claim
                                            </span>
                                        @endif

                                        @if($canSpin&&$winner['ledger_id'])
                                            <span class="text-[10px] text-gray-400">PK ledger #{{ $winner['ledger_id'] }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($canSpin)
                                    <div class="flex shrink-0 flex-col items-end gap-1">
                                        @if(($winner['claim_status']??'Pending')!=='Completed')
                                            <button type="button"
                                                wire:click="markClaimed({{ $winner['WinnerID'] }})"
                                                wire:confirm="Tag this prize as claimed? Its ₱{{ number_format($winner['prize_amount'],2) }} Pangkabuhayan ledger entry becomes Completed."
                                                class="whitespace-nowrap rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-[10px] font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                                ✅ Mark claimed
                                            </button>
                                        @endif

                                        <button type="button"
                                            wire:click="resendWinnerEmail({{ $winner['WinnerID'] }})"
                                            title="Email this winner again"
                                            class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-[11px] text-gray-500 transition hover:border-[#0C2C6B] hover:text-[#0C2C6B]">
                                            ✉️
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($canSpin)
                        @if(($summary['pending_count']??0)>0)
                            <button type="button" wire:click="markAllClaimed"
                                wire:confirm="Tag all {{ $summary['pending_count'] }} pending prize(s) in this cycle as claimed?"
                                class="mt-3 w-full rounded-lg bg-emerald-600 px-3 py-2 text-[11px] font-semibold text-white transition hover:bg-emerald-700">
                                ✅ Mark all {{ $summary['pending_count'] }} pending as claimed
                            </button>
                        @endif

                        <button type="button" wire:click="undoLast"
                            wire:confirm="Remove the most recent winner from this cycle?"
                            class="mt-3 w-full rounded-lg border border-red-200 px-3 py-2 text-[11px] font-semibold text-red-600 transition hover:bg-red-50">
                            ↩︎ Undo last spin
                        </button>

                        <p class="mt-3 border-t border-gray-100 pt-3 text-[10px] leading-relaxed text-gray-400">
                            Every winner is booked in the ledger as a
                            <strong>₱{{ number_format($summary['prize_amount']??0,2) }}</strong>
                            Pangkabuhayan expense (<strong>Source PK</strong>, RefID = member ID, DrawID = this cycle)
                            and stays <strong>Pending</strong> until the prize is claimed.
                            Claimed ₱{{ number_format($summary['claimed_cost']??0,2) }}
                            · pending ₱{{ number_format($summary['pending_cost']??0,2) }}.
                        </p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endif
</div>
