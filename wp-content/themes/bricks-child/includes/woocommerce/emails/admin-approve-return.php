<?php

class WC_Admin_Approve_Return extends WC_Email {
    protected string $return_id;

    public function __construct() {
        // Define ID, title, and description
        $this->id             = 'admin_approve_return';
        $this->title          = 'Admin Approve Return Email';
        $this->description    = 'An email sent to the customer when their return request is approved by the admin.';

        // Define heading and subject
        $this->heading        = 'Admin Approved Return';
        $this->subject        = 'Your Return Request has been Approved';

        // Locate and load the email template
        $this->template_html  = 'emails/admin-approve-return.php';
        $this->template_plain = 'emails/plain/admin-approve-return.php';

        // Trigger email send
        add_action('send_admin_approve_return_notification', array($this, 'trigger'), 10, 3);

        parent::__construct();
    }

    // Hàm kích hoạt để gửi email
    public function trigger($order_id, $order = false, $requestId = false) {
        if (!$order_id) {
            return;
        }

        $this->object = wc_get_order($order_id);

        if (!$this->object) {
            return;
        }

        if ($order) {
            $this->object = $order;
        }

        $this->setReturnId($requestId);

        $this->recipient = $this->object->get_billing_email();

        if (!$this->is_enabled() || !$this->get_recipient()) {
            return;
        }

        $result = $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }

    // Lấy nội dung của email
    public function get_content_html() {
        ob_start();
        $return_data = get_return_request_by_id($this->object->get_id(),$this->getReturnId());
        wc_get_template($this->template_html, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => false,
            'email'         => $this,
            'return_data'   => $return_data
        ));
        return ob_get_clean();
    }

    public function get_content_plain() {
        ob_start();
        wc_get_template($this->template_plain, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => true,
            'email'         => $this
        ));
        return ob_get_clean();
    }

    // Tùy chỉnh các tùy chọn cài đặt email
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'         => 'Activate',
                'type'          => 'checkbox',
                'label'         => 'Activate this notification',
                'default'       => 'no',
            ),
            'email_type' => array(
                'title'         => 'Email Type',
                'type'          => 'select',
                'description'   => 'Choose which format of email to send.',
                'default'       => 'html',
                'options'       => array(
                    'plain'     => 'Plain text',
                    'html'      => 'HTML',
                    'multipart' => 'Multipart',
                ),
            ),
            // Add other options if needed
        );
    }

    /**
     * @return mixed
     */
    public function getReturnId()
    {
        return $this->return_id;
    }

    /**
     * @param mixed $return_id
     */
    public function setReturnId($return_id): void
    {
        $this->return_id = $return_id;
    }
}
