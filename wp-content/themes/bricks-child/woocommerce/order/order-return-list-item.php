<div class="md:container mx-auto px-8">
    <?php if (!wp_is_mobile()): ?>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 border-b">
                <thead>
                <tr>
                    <th class="w-1/2 text-left font-medium tracking-wider">Item Description</th>
                    <th class="px-6 text-left font-medium tracking-wider">Quantity</th>
                    <th class="px-6 font-medium tracking-wider" style="text-align: right">Price</th>
                    <!-- Add more columns if needed -->
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                <template x-for="item in items">
                    <?php wc_get_template('order/order-return-item-table-row.php'); ?>
                </template>

                <!-- Add more data rows if needed -->
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="flex flex-col divide-y border-b">
            <template x-for="item in items">
                <?php wc_get_template('order/order-return-item.php'); ?>
            </template>
        </div>
    <?php endif; ?>
    <div class="flex flex-col mt-10 items-start gap-10">
        <div class="lg:grid lg:grid-cols-1 bg-gray-100 space-y-3 py-6 rounded-lg w-full lg:w-1/3 px-8">
            <div class="flex justify-between items-center">
                <h2 class="text-base font-normal">Subtotal:</h2>
                <p class="text-sm" x-text="formatCurrency(subtotal)">0</p>
            </div>

            <div class="flex justify-between items-center">
                <h2 class="text-base font-normal">Taxes(TX):</h2>
                <p class="text-sm" x-text='formatCurrency(tax)'>0</p>
            </div>

            <div class="border-t border-black px-8"></div>

            <div class="flex justify-between items-center">
                <h2 class="text-base font-normal">Refund Total:</h2>
                <p class="text-sm" x-text='formatCurrency(returnTotal)'>0</p>
            </div>
        </div>
        <div class="w-full lg:w-1/3">
            <button style="text-transform: none;" class="button slim w-full normal-case" @click="createReturnRequest()">Create a Return</button>
        </div>
    </div>
</div>
