<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

$wc_emails = WC_Emails::instance();
$emails = $wc_emails->get_emails();

$order = wc_get_order(5701); // Thay '123' bằng ID của một đơn hàng cụ thể

$template = isset($_GET['template']) ? $_GET['template'] : 'return_confirmation';

switch ($template) {
    case 'return_confirmation':
        $returnConfirmation = $emails['WC_Return_Confirmation'];
        $returnConfirmation->setReturnId('66066983f0320'); // Thay '1' bằng ID của một yêu cầu trả hàng cụ thể
        $returnConfirmation->object = $order;

        echo apply_filters( 'woocommerce_mail_content', $returnConfirmation->style_inline( $returnConfirmation->get_content_html() ) );

        // Gửi email
        $returnConfirmation->send($order->get_billing_email(), $returnConfirmation->get_subject(), $returnConfirmation->get_content(), $returnConfirmation->get_headers(), $returnConfirmation->get_attachments());
        break;

    case 'admin_approve_return':
        $adminApproveReturn = $emails['WC_Admin_Approve_Return'];
        $adminApproveReturn->setReturnId('66066983f0320'); // Thay '1' bằng ID của một yêu cầu trả hàng cụ thể
        $adminApproveReturn->object = $order;

        echo apply_filters( 'woocommerce_mail_content', $adminApproveReturn->style_inline( $adminApproveReturn->get_content_html() ) );

        // Gửi email
        $adminApproveReturn->send($order->get_billing_email(), $adminApproveReturn->get_subject(), $adminApproveReturn->get_content(), $adminApproveReturn->get_headers(), $adminApproveReturn->get_attachments());
        break;
    case 'admin_new_return':
        $adminNewReturn = $emails['WC_Admin_New_Return'];
        $adminNewReturn->setReturnId('66066983f0320'); // Thay '1' bằng ID của một yêu cầu trả hàng cụ thể
        $adminNewReturn->object = $order;

        echo apply_filters( 'woocommerce_mail_content', $adminNewReturn->style_inline( $adminNewReturn->get_content_html() ) );

        // Gửi email
        $adminNewReturn->send($order->get_billing_email(), $adminNewReturn->get_subject(), $adminNewReturn->get_content(), $adminNewReturn->get_headers(), $adminNewReturn->get_attachments());
        break;

    default:
        echo 'Invalid template';
        break;
}