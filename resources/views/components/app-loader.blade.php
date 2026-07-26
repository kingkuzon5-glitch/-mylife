<div
    x-data
    x-show="$store.axiomLoader.visible"
    x-transition.opacity.duration.200ms
    style="display: none"
    class="fixed inset-0 z-[100] flex items-center justify-center bg-background/70 backdrop-blur-sm"
>
    <div class="relative flex items-center justify-center w-24 h-24">
        <div class="axiom-loader-glow"></div>
        <div class="axiom-loader-ring"></div>
        <span class="relative text-[10px] font-extrabold tracking-[0.2em] text-on-surface">AXIOM</span>
    </div>
</div>
