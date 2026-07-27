import './bootstrap';

document.addEventListener('alpine:init', () => {
    Alpine.store('axiomLoader', { visible: false });
});
