<tr>
    <td class="py-6 whitespace-nowrap align-top">
        <div class="flex flex-col lg:flex-row justify-between gap-5">
            <div class="flex flex-row items-start lg:space-x-4 lg:gap-7 gap-4">
                <div class="flex items-center gap-5">
                    <div x-html="_.unescape(item.image)" class="w-[150px] overflow-hidden flex justify-center"></div>
                </div>
                <div class="flex flex-col justify-between">
                    <a :href="item.permalink" class="text-sm sm:text-base" x-text="item.name"></a>
                    <div x-html="_.unescape(item.metaData)"></div>
                </div>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap align-top" colspan="2" >
        <div class="grid-cols-2 grid gap-8">
            <div class="text-left">
                <div class="text-left">
                    <div class="relative">
                        <div class="relative">
                            <select
                                    @change="calRefundTotal()"
                                    name="qty[]"
                                    x-model="item.selectedReturnQuantity"
                                    class="block w-[80px] border-none"
                                    x-bind:disabled="item.qty - item.returnedQuantity === 0">
                                <option value="" x-text="0"></option>
                                <template x-for="n in item.qty - item.returnedQuantity" :key="n">
                                    <option :value="n" x-text="n"></option>
                                </template>
                            </select>
                            <span class="border-b border-black block absolute bottom-2 w-[45px]"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right" x-text="formatCurrency(item.subTotalFormatted)"></div>
            <div class="col-span-2">
                <select id="reason" x-model="item.reason" class="block appearance-none">
                    <template x-for="(value, key) in Object.entries(returnReason)" :key="key">
                        <option x-bind:value="value[0]" x-text="value[1]"></option>
                    </template>
                </select>
                <textarea placeholder="Feedback (optional)" class="mt-3" x-model="item.feedback">Feedback(optional)</textarea>
            </div>
        </div>
    </td>
</tr>

