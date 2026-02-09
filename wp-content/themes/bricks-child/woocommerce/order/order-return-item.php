<!-- This template is specifically designed for mobile views.-->
<div class="flex flex-col lg:flex-row justify-between gap-5 py-8">
    <div class="flex flex-row items-start lg:space-x-4 lg:gap-7 gap-4">
        <div class="flex items-center gap-5">
            <div x-html="_.unescape(item.image)" class="w-[150px] overflow-hidden flex justify-center"></div>
        </div>

        <div class="flex flex-col justify-between leading-base">
            <a :href="item.permalink" class="text-sm md:text-lg " x-text="item.name"></a>

            <div class="text-sm">
                <div x-html="_.unescape(item.metaData)"></div>
                <div></div>
            </div>

            <div class="flex flex-row gap-2 items-end text-sm py-2">
                <label class="text-charcoal m-0 leading-6">Qty:</label>
                <div class="relative">
                    <select
                            @change="calRefundTotal()"
                            name="qty[]"
                            x-model="item.selectedReturnQuantity"
                            class="block w-[70px] border-none leading-6 text-xs"
                            x-bind:disabled="item.qty - item.returnedQuantity === 0">
                        <option value="" x-text="0"></option>
                        <template x-for="n in item.qty - item.returnedQuantity" :key="n">
                            <option :value="n" x-text="n"></option>
                        </template>
                    </select>
                    <span class="border-b border-black block absolute bottom-0 w-2/3"></span>
                </div>
            </div>

            <span class="text-sm" x-text="formatCurrency(item.subTotalFormatted)"></span>

        </div>
    </div>
    <div class="flex flex-col gap-5 text-sm">
        <select id="reason" x-model="item.reason" class="block appearance-none rounded-lg">
            <template x-for="(value, key) in Object.entries(returnReason)" :key="key">
                <option x-bind:value="value[0]" x-text="value[1]"></option>
            </template>
        </select>
        <textarea placeholder="Feedback (optional)" class="mt-3 rounded-lg" x-model="item.feedback">Feedback(optional)</textarea>
    </div>
</div>