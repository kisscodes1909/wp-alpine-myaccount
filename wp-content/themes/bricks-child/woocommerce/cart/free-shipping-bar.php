<div class="w-full mt-3" x-data="freeShippingBar">
    <div class="text-gray-700 mb-2" x-text="textMessage">
        You're $49 away from Free Standard Shipping
    </div>
    <div class="relative w-full h-3 bg-gray-200 overflow-hidden">
        <div :style="{ width: progress + '%' }" id="progress-bar" class="absolute top-0 left-0 h-full bg-gold-gradient transition-all duration-1000"></div>
    </div>
    <div class="flex justify-between text-gray-500 mt-1">
        <span>$0</span>
        <span>$35</span>
    </div>
</div>

<script>
    // document.addEventListener('alpine:init', () => {
        Alpine.data('freeShippingBar', () => ({
            cartTotal: <?php echo WC()->cart->get_subtotal(); ?>,
            freeShippingCost: 35,
            progress: 0,
            textMessage: '',
            setProgress(value) {
                this.progress = value;
            },
            initialProgress() {
                this.setProgress(this.cartTotal / this.freeShippingCost * 100);
            },
            initTextMessage() {
                if (this.cartTotal >= this.freeShippingCost) {
                    this.textMessage = 'You’ve qualify for free shipping';
                } else {
                    this.textMessage = 'You\'re $' + (this.freeShippingCost - this.cartTotal).toFixed(2) + ' away from Free Standard Shipping';
                }
            },
            init() {
                this.initTextMessage();
                setTimeout(() => {
                    this.initialProgress();
                }, 500);
            }
        }));
    // });
</script>