<!-- Popup: center only. x-html content is compiled with Alpine so directives/events work. -->
<template x-data x-teleport="#popup-container">
    <div
            x-show="$store.popup.open"
            x-transition:enter="transition duration-200 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 flex items-center justify-center p-4 bg-black/50"
            aria-labelledby="modal-title" role="dialog" aria-modal="true"
            @click.self="$store.popup.closePopup()"
            x-effect="$store.popup.open && $store.popup.content && $nextTick(() => {
                const el = document.getElementById('popup-content-wrap');
                if (el && el.children.length && typeof Alpine !== 'undefined' && Alpine.initTree) Alpine.initTree(el);
            })">
        <div id="popup-content-wrap" class="relative z-20 bg-white overflow-auto w-full max-w-[700px] px-5 p-8 md:p-8" x-html="$store.popup.content"></div>
    </div>
</template>
