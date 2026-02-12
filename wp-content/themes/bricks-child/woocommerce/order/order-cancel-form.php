<div class="mb-14 flex-row justify-between items-center">
<!--    <a href="--><?php //echo wc_get_endpoint_url('view-order', $order->get_id()) ?><!--">-->
<!--        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="12" viewBox="0 0 7 12" fill="none">-->
<!--            <path d="M5.5 0.803711L1.25447 5.21907C0.94122 5.54484 0.94122 6.07303 1.25447 6.39881L5.5 10.8142" stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"/>-->
<!--        </svg>-->
<!--    </a>-->
    <h1 class="text-2xl font-bold lg:text-center">Order Cancellation</h1>
</div>


<form class="mb-4 max-w-[700px] mx-auto" method="post">

    <?php wc_print_notices(); wc_clear_notices()?>

    <div class="mb-4">
        <label for="reason" class="block text-gray-700 text-sm font-bold mb-2">
            Reason for canceling
        </label>
        <div class="relative">
            <select id="reason" name="reason" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 py-3 px-4 pr-8 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                <?php foreach ($cancellation_reasons as $key => $value): ?>
                    <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($value); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php wp_nonce_field( 'cancel-order', 'cancel-order-nonce' ); ?>

    <div class="mb-6">
        <textarea id="message" name="message" rows="8" class="appearance-none border w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Write message here (optional)..."></textarea>
    </div>

    <div class="flex items-center justify-end">
        <button class="button slim" type="submit">
            Submit
        </button>
    </div>
</form>
