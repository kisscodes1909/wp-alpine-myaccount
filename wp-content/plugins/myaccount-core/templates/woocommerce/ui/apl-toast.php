<!-- Toast -->
<template x-data x-teleport="#toast-container" x-init="$store.toast.toasts = []">
    <template x-for="toast in $store.toast.toasts" :key="toast.id">
        <div
                x-show="toast.show"
                :class="toast.type"
                x-text="toast.message"
                x-transition:enter="ma-tr-enter"
                x-transition:enter-start="ma-tr-enter-start"
                x-transition:enter-end="ma-tr-enter-end"
                x-transition:leave="ma-tr-leave"
                x-transition:leave-start="ma-tr-leave-start"
                x-transition:leave-end="ma-tr-leave-end"
                class="ma-ui-toast__item"
        >
        </div>
    </template>
</template>
