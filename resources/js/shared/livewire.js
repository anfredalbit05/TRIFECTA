/**
 * Small helpers for talking to Livewire components from external page scripts.
 *
 * Blade's `@this` directive only exists inside compiled views, so extracted
 * scripts resolve the component by its Livewire name instead.
 */

export function findLivewire(name) {
    const el = [...document.querySelectorAll('[wire\\:id]')].find(
        (el) => JSON.parse(el.getAttribute('wire:snapshot') || '{}').memo?.name === name,
    );

    return el ? window.Livewire.find(el.getAttribute('wire:id')) : null;
}

export function livewireCall(name, method, ...args) {
    const wire = findLivewire(name);

    if (!wire) {
        return false;
    }

    wire.call(method, ...args);

    return true;
}

export function livewireGet(name, property) {
    const wire = findLivewire(name);

    return wire ? wire.get(property) : Promise.resolve(undefined);
}
