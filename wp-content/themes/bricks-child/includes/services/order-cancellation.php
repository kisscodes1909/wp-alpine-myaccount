<?php

class Order_Cancellation {
    private string $currentStage;
    private string $currentLabel;

    private WC_DateTime $processingAt;

    private WC_DateTime $shippedAt;

//    private array $stages = [
//        'before-processing',
//        'in-30min-processing',
//        'after-30min-processing',
//        'in-shipping',
//        'in-30days-shipped',
//        'completed'
//    ];
    private WC_Order $order;
    public function __construct(WC_Order $order)
    {
        $this->order = $order;

        $this->loadData();
        $this->determineStage();
    }

    public function getCurrentLabel(): string
    {
        return $this->currentLabel;
    }

    public function setCurrentLabel(string $currentLabel): void
    {
        $this->currentLabel = $currentLabel;
    }

    public function getProcessingAt(): WC_DateTime
    {
        return $this->processingAt;
    }

    public function setProcessingAt(WC_DateTime $processingAt): void
    {
        $this->processingAt = $processingAt;
    }

    public function getshippedAt(): WC_DateTime
    {
        return $this->shippedAt;
    }

    public function setshippedAt(WC_DateTime $shippedAt): void
    {
        $this->shippedAt = $shippedAt;
    }

    /**
     * @return mixed
     */
    public function getCurrentStage()
    {
        return $this->currentStage;
    }

    /**
     * @param mixed $currentStage
     */
    public function setCurrentStage($currentStage): void
    {
        $this->currentStage = $currentStage;
    }

    private function cancel() {

        $curentStage = $this->getCurrentStage();

        if($curentStage === 'before-processing') {
            $this->autoCancel();
        } elseif ($curentStage === 'in-30min-processing') {
            $this->autoRefund();
        } else {
            $this->sendCancelRequest();
        }

    }

    private function autoRefund() {

    }

    private function autoCancel() {

    }

    private function sendCancelRequest() {

    }

    public function determineStage(): void
    {
        //Set Current Stage,
        //Set Current Label

        // Phải dựa vào dữ liệu
        if($this->order->get_status('pending-payment') || $this->order->get_status('failed')) {
            $this->setCurrentStage('before-processing');
            $this->setCurrentLabel('cancel');
        }

        if($this->order->get_status('processing') && (current_time('now') - $this->getProcessingAt()->getTimestamp() < 1800) ) {
            $this->setCurrentStage('in-30min-processing');
            $this->setCurrentLabel('cancel');
        }

        if($this->order->get_status('processing') && (current_time('now') - $this->getProcessingAt()->getTimestamp() > 1800) ) {
            $this->setCurrentStage('after-30min-processing');
            $this->setCurrentLabel('cancel');
        }

        if($this->order->get_status('shipping')) {
            $this->setCurrentStage('in-shipping');
            $this->setCurrentLabel('cancel');
        }

        if($this->order->get_status('shipped') && (current_time('now') - $this->getshippedAt()->getTimestamp() < 2592000) ) {
            $this->setCurrentStage('in-30days-shipped');
            $this->setCurrentLabel('return');

        }
        if($this->order->get_status('shipped') && (current_time('now') - $this->getshippedAt()->getTimestamp() < 2592000) ) {
            $this->setCurrentStage('completed');
        }

    }

    function loadData() {
        $timeStampProcessingAt = $this->order->get_meta('processingAt'); // Format timestamp
        $timeStampShippedAt = $this->order->get_meta('shippedAt'); // Format timestamp
        if($timeStampProcessingAt) {
            $this->setProcessingAt(new WC_DateTime("@$timeStampProcessingAt"));
        }

        if($timeStampShippedAt) {
            $this->setshippedAt(new WC_DateTime("@$timeStampShippedAt"));
        }
    }
}