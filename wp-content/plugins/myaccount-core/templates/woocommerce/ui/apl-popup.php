<!-- Popup: Escape to close, click backdrop to close, focus first focusable, panel scale animation -->
<template x-data x-teleport="#popup-container">
    <div
            x-show="$store.popup.open"
            x-transition:enter="ma-tr-enter"
            x-transition:enter-start="ma-tr-enter-start"
            x-transition:enter-end="ma-tr-enter-end"
            x-transition:leave="ma-tr-leave"
            x-transition:leave-start="ma-tr-leave-start"
            x-transition:leave-end="ma-tr-leave-end"
            class="ma-u-backdrop ma-ui-popup__overlay"
            role="dialog"
            aria-modal="true"
            aria-label="<?php echo esc_attr__( 'Dialog', 'woocommerce' ); ?>"
            @click.self="$store.popup.closePopup()"
            @keydown.escape.window="$store.popup.open && $store.popup.closePopup()"
            x-effect="$store.popup.open && $store.popup.content && $nextTick(() => {
                const el = document.getElementById('popup-content-wrap');
                if (el && el.children.length && typeof Alpine !== 'undefined' && Alpine.initTree) Alpine.initTree(el);
                const first = el && el.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex=\'-1\'])');
                if (first && typeof first.focus === 'function') first.focus();
            })">
        <div
                id="popup-content-wrap"
                class="ma-ui-popup__panel"
                x-show="$store.popup.open"
                x-transition:enter="ma-tr-enter ma-tr-scale-enter"
                x-transition:enter-start="ma-tr-enter-start ma-tr-scale-enter-start"
                x-transition:enter-end="ma-tr-enter-end ma-tr-scale-enter-end"
                x-transition:leave="ma-tr-leave ma-tr-scale-leave"
                x-transition:leave-start="ma-tr-leave-start ma-tr-scale-leave-start"
                x-transition:leave-end="ma-tr-leave-end ma-tr-scale-leave-end"
                x-html="$store.popup.content"
                @click.stop>
        </div>
    </div>
</template>
