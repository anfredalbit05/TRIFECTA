{{-- Reusable Year → Month → Day picker. Vars: $model (Livewire property), $label, $title --}}
<div wire:key="dp-{{ $model }}-{{ $userId ?? 'new' }}" class="relative"
    x-data="{
        v: @entangle($model).live, open: false, step: 'year', y: '2000', m: '01', d: '01',
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        get key() { return { year: 'y', month: 'm', day: 'd' }[this.step] },
        get f() { return `${this.y}-${this.m}-${this.d}` },
        get maxDay() { return new Date(+this.y, +this.m, 0).getDate() },
        get items() {
            if (this.step === 'year') return Array.from({ length: 100 }, (_, i) => { const v = String(new Date().getFullYear() - i); return { l: v, v } });
            if (this.step === 'month') return this.months.map((l, i) => ({ l, v: String(i + 1).padStart(2, '0') }));
            return Array.from({ length: this.maxDay }, (_, i) => { const v = String(i + 1).padStart(2, '0'); return { l: v, v } });
        },
        sync() { const p = (this.v || '').split('-'); if (p.length === 3) [this.y, this.m, this.d] = p },
        init() { this.sync(); this.$watch('v', () => this.sync()) },
        pick(v) {
            this[this.key] = v;
            if (+this.d > this.maxDay) this.d = String(this.maxDay).padStart(2, '0');
            if (this.step !== 'day') this.step = this.step === 'year' ? 'month' : 'day';
        },
        ok() { this.v = this.f; this.open = false; this.step = 'year' }
    }">
    <label class="{{ $lbl }}">{{ $label }}</label>
    <button type="button" @click="open = true"
        class="flex w-full items-center justify-between rounded-xl border border-slate-300 bg-white px-3 py-2 text-left text-sm transition hover:border-[var(--color-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
        <span x-text="v || 'Select Date'" :class="v ? 'text-slate-700' : 'text-slate-500'"></span>
        <span class="text-slate-500">&#128197;</span>
    </button>

    <div x-show="open" x-cloak x-transition.opacity
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
        <div @click.stop class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="bg-[var(--color-accent)] p-5 text-white">
                <span class="text-[10px] font-bold uppercase tracking-widest opacity-80">{{ $title }}</span>
                <h2 class="mt-1 text-xl font-extrabold" x-text="f"></h2>
                <div class="mt-3 flex gap-4 text-xs font-bold capitalize">
                    <template x-for="s in ['year', 'month', 'day']" :key="s">
                        <button type="button" @click="step = s" x-text="s"
                            :class="step === s ? 'underline opacity-100' : 'opacity-60'"></button>
                    </template>
                </div>
            </div>

            <div class="grid h-64 gap-2 overflow-y-auto p-4 text-center"
                :class="{ year: 'grid-cols-3', month: 'grid-cols-2', day: 'grid-cols-5' }[step]">
                <template x-for="it in items" :key="step + it.v">
                    <button type="button" @click="pick(it.v)" x-text="it.l"
                        :class="this[key] === it.v ? 'bg-[var(--color-primary)] font-bold text-white' : 'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="rounded-lg py-2.5 text-sm transition"></button>
                </template>
            </div>

            <div class="flex items-center justify-between border-t bg-slate-50 px-5 py-3">
                <button type="button" @click="open = false; step = 'year'"
                    class="rounded-lg px-4 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-200">Cancel</button>
                <button type="button" @click="ok()"
                    class="rounded-lg bg-[var(--color-primary)] px-6 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[var(--color-primary-mid)]">OK</button>
            </div>
        </div>
    </div>
</div>
