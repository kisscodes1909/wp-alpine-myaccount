<?php
$returnItems = classifyOrderItemsForReturn($order);

$alpineData = [
        'returnReason' => get_return_reasons(),
        'items' => $returnItems['ValidForReturn'] ?? [],
        'orderId' => $order->get_id()
];


?>


<?php if(count($returnItems['ValidForReturn']) > 0):  ?>
    <div x-data="returnOrder">
        <?php wc_get_template('myaccount/page-heading.php',
            [
                'page_heading' => 'Start a Return',
                'prev_page' => [
                    'title' => 'Order Details',
                    'url'   => wc_get_endpoint_url('view-order', $order->get_id())
                ]
            ]
        ); ?>
        <?php
            wc_get_template('woocommerce/order/order-return-list-item.php');
        ?>
    </div>

    <?php
    wc_get_template('ui/apl-popup.php');
    wc_get_template('ui/apl-toast.php');
    wc_get_template('ui/apl-loader.php');
    ?>
<?php endif; ?>

<?php if(count($returnItems['IneligibleItem']) > 0):  ?>
    <?php wc_get_template('myaccount/page-heading.php', [
            'page_heading' => 'Ineligible for Return',
            'prev_page' => [
                'title' => 'Order Details',
                'url'   => wc_get_endpoint_url('view-order', $order->get_id())
            ]
        ]); ?>

    <?php
        wc_get_template('woocommerce/order/order-details-list-item.php', [
            'order_items' => $returnItems['IneligibleItem'],
            'order' => $order,
        ]);
    ?>

<?php endif; ?>

<script>
    let alpineData = JSON.parse('<?php echo json_encode($alpineData) ?>');

    alpineData.items = Object.values(alpineData.items);

    document.addEventListener('alpine:init', () => {
        Alpine.data('returnOrder', () => ({
                ...alpineData,
                subtotal:0,
                tax:0,
                returnTotal:0,
                init() {
                    this.calRefundTotal();
                },
                calSubtotal() {
                    // Tính toán subtotal cho các items dựa trên số lượng đã chọn để trả thêm
                    this.subtotal = this.items.reduce((total, item) => {
                        return total + (item.subTotalExcludingTax * (parseInt(item.selectedReturnQuantity) + parseInt(item.returnedQuantity)));
                    }, 0);
                },
                calTax() {
                    // Tính toán thuế cho các items dựa trên số lượng đã chọn để trả thêm
                    this.tax = this.items.reduce((total, item) => {
                        return total + (item.taxAmount * (parseInt(item.selectedReturnQuantity) + parseInt(item.returnedQuantity)));
                    }, 0);
                },
                calRefundTotal() {
                    // Tính toán tổng số tiền hoàn lại bao gồm cả thuế cho các items dựa trên số lượng đã chọn để trả thêm
                    // console.log(this.subtotal);
                    this.calSubtotal();
                    this.calTax();
                    // console.log(this.subtotal);

                    this.returnTotal = this.subtotal + this.tax;
                },
                formatCurrency(value) {
                    // Sử dụng accounting.js để format giá
                    return accounting.formatMoney(value, { symbol: "$",  format: "%s%v", thousand: ",", decimal: "." });
                },
                async createReturnRequest() {
                    // Show loader
                    this.$store.loader.show();

                    const selectedItems = this.items.filter(item => item.selectedReturnQuantity > 0);

                    const items = selectedItems.map(item => {
                        return {
                            id: item.id,
                            name: item.name,
                            selectedReturnQuantity: item.selectedReturnQuantity,
                            reason: item.reason,
                            feedback: item.feedback,
                            // Bạn có thể thêm các thông tin khác cần thiết ở đây
                        };
                    });

                    try {
                        const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                action: 'handle_return_request',
                                nonce: '<?php echo wp_create_nonce('return_order'); ?>', // Add the nonce here
                                data: JSON.stringify({
                                    items,
                                    orderId: this.orderId
                                })
                            })
                        });

                        const responseData = await response.json();

                        if (responseData.success) {
                            this.$store.toast.addToast(responseData.data, 'success');
                        } else {
                            this.$store.toast.addToast('Error updating account details', 'error');
                        }

                        this.$store.loader.hide();
                    } catch (error) {
                        console.log(error);
                        this.$store.loader.hide();
                        this.$store.toast.addToast('AJAX request failed', 'error');
                    }
                }
            }
        ));
    })

</script>

