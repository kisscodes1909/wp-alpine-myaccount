<!-- Popup -->
<template x-data x-teleport="#popup-container">
    <div
            x-show="$store.popup.open"
            x-transition:enter="ma-tr-enter"
            x-transition:enter-start="ma-tr-enter-start"
            x-transition:enter-end="ma-tr-enter-end"
            x-transition:leave="ma-tr-leave"
            x-transition:leave-start="ma-tr-leave-start"
            x-transition:leave-end="ma-tr-leave-end"
            class="ma-ui-popup__overlay"
            aria-labelledby="modal-title" role="dialog" aria-modal="true"
            @click.self="$store.popup.closePopup()"
            x-effect="$store.popup.open && $store.popup.content && $nextTick(() => {
                const el = document.getElementById('popup-content-wrap');
                if (el && el.children.length && typeof Alpine !== 'undefined' && Alpine.initTree) Alpine.initTree(el);
            })">
        <div id="popup-content-wrap" class="ma-ui-popup__panel" x-html="$store.popup.content"></div>
    </div>
</template>
