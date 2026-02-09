<!-- Toast -->
<!-- Toast store is now registered in assets/js/alpine/stores/toast.js -->
<template x-data x-teleport="#toast-container" x-init="$store.toast.toasts = []">
    <template x-for="toast in $store.toast.toasts" :key="toast.id">
        <div
                x-show="toast.show"
                :class="toast.type"
                x-text="toast.message"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-300 transform"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed top-14 right-14 bg-black text-white py-5 px-7 rounded shadow-lg"
        >
            <!-- Toast message will be displayed here -->
        </div>
    </template>
</template>