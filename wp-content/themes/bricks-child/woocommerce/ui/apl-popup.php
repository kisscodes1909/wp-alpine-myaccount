<!-- Popup: center only, KISS. openPopup(content) -->
<template x-data x-teleport="#popup-container">
    <div
            x-show="$store.popup.open"
            x-transition:enter="transition ease-in-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in-out duration-200 delay-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 w-full h-full pointer-events-none flex items-center justify-center p-4"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div @click="$store.popup.closePopup()" class="fixed inset-0 bg-black/80 bg-opacity-50 pointer-events-auto"></div>
        <div
                x-show="$store.popup.open"
                class="relative z-20 bg-white rounded-xl overflow-auto w-full max-w-[700px] max-h-[95vh] min-h-[50vh] px-5 p-8 md:p-8 pointer-events-auto"
                x-transition:enter="transition ease-in-out duration-200 delay-50"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in-out duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-html="$store.popup.content">
        </div>
    </div>
</template>
