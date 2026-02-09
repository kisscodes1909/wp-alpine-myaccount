<!-- Global Popup Structure -->
<template x-data x-teleport="#popup-container">
    <!-- Wrapper fixed inset-0 so overlay is always full viewport; avoids misalignment from parent flex/transform -->
    <div
            x-show="$store.popup.open"
            x-transition:enter="transition duration-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-100 delay-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 w-full h-full pointer-events-none"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div @click="$store.popup.closePopup()" class="fixed inset-0 bg-black/80 bg-opacity-50 transition-opacity pointer-events-auto"></div>
        <!-- Right To Left Direction -->
        <template x-if="$store.popup.direction == 'right'" >
            <div class="fixed bottom-0 max-h-[95%] h-fit transform lg:right-0 lg:top-1/2 lg:-translate-y-1/2 w-full lg:w-auto lg:h-full lg:max-h-none pointer-events-auto">
                <div
                        x-show="$store.popup.open"
                        class="bg-white rounded-tl-xl rounded-tr-xl lg:rounded-none z-20 overflow-auto w-full h-full sm:w-[700px] px-5 p-8 md:p-8 "
                        x-transition:enter="transition duration-200 delay-100"
                        x-transition:enter-start="transform translate-y-full lg:translate-x-full lg:translate-y-0 "
                        x-transition:enter-end="transform translate-y-0 lg:translate-x-0 lg:translate-y-0"
                        x-transition:leave="transition duration-200"
                        x-transition:leave-start="transform translate-y-0 lg:translate-x-0 lg:translate-y-0"
                        x-transition:leave-end="transform translate-y-full lg:translate-x-full lg:translate-y-0"
                        x-html="$store.popup.content"
                >
                </div>
            </div>
        </template>
        <!-- Left To Right Direction -->
        <template x-if="$store.popup.direction == 'left'" >
            <div class="fixed bottom-0 max-h-[95%] h-fit transform lg:left-0 lg:top-1/2 lg:-translate-y-1/2 w-full lg:w-auto lg:h-full lg:max-h-none pointer-events-auto">
                <div
                        class="bg-white rounded-tl-xl rounded-tr-xl lg:rounded-none z-20 overflow-auto w-full h-full sm:w-[700px] px-5 p-8 md:p-8 "
                        x-show="$store.popup.open"
                        x-transition:enter="transition duration-200 delay-100"
                        x-transition:enter-start="transform translate-y-full lg:-translate-x-full lg:translate-y-0 "
                        x-transition:enter-end="transform translate-y-0 lg:translate-x-0 lg:translate-y-0"
                        x-transition:leave="transition duration-200"
                        x-transition:leave-start="transform translate-y-0 lg:translate-x-0 lg:translate-y-0"
                        x-transition:leave-end="transform translate-y-full lg:-translate-x-full lg:translate-y-0"
                        x-html="$store.popup.content"
                >
                </div>
            </div>
        </template>

        <!-- Center -->
        <template x-if="$store.popup.direction == 'center'" >
            <div class="fixed bottom-0 lg:left-1/2 transform lg:top-1/2 lg:-translate-x-1/2 lg:-translate-y-1/2 lg:bottom-auto w-full lg:w-auto max-h-[95%] h-fit pointer-events-auto">
                <div
                        class="bg-white rounded-tl-xl rounded-tr-xl lg:rounded-bl-xl lg:rounded-br-xl z-20 overflow-auto w-full sm:w-[700px] px-5 p-8 md:p-8"
                        x-show="$store.popup.open"
                        x-transition:enter="transition duration-200 delay-100"
                        x-transition:enter-start="transform translate-y-full lg:translate-y-0"
                        x-transition:enter-end="transform translate-y-0"
                        x-transition:leave="transition duration-200"
                        x-transition:leave-start="transform translate-y-0"
                        x-transition:leave-end="transform translate-y-full lg:translate-y-0"
                        x-html="$store.popup.content">
                </div>
            </div>
        </template>
    </div>
</template>

<!-- Popup store is now registered in assets/js/alpine/stores/popup.js -->