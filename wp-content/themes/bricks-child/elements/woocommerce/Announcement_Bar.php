<?php
// TODO: Optimize This module
class Announcement_Bar extends \Bricks\Element {
	public $name            = 'woocommerce-announcement-bar';
	public $icon            = 'fas fa-passport';
	public $category        = 'woocommerce';

	public function get_label() {
		return esc_html__( 'Announcement Bar', 'bricks' );
	}

	public function set_controls() {
		$this->controls['notifications'] = [
			'tab' => 'content',
			'label' => esc_html__( 'Notifications', 'bricks' ),
			'type' => 'repeater',
			'contentProperty' => 'notification', // Default 'content'
			'placeholder' => esc_html__( 'Notification', 'bricks' ),
			'fields' => [
				'content' => [
					'label' => esc_html__( 'Content', 'bricks' ),
					'type' => 'text',
				],
				'url' => [
					'label' => esc_html__( 'Url', 'bricks' ),
					'type'  => 'text',
				]
			],
		];
	}

	// Render element HTML
	public function render() {

		$items = $this->settings['notifications'];
		$output = '';

        $this->set_attribute('_root', 'class', 'relative overflow-hidden h-[50px] w-full');

		if (is_array($items) && count($items)) {
			$output .= "<div {$this->render_attributes( '_root' )}><ul id='announcement-container' class=' list-none p-0 m-0 absolute top-0 left-0 w-full transition-transform duration-500'>";
			foreach ($items as $item) {
				$item['url'] = $item['url'] ?? '';
				$output .= '<li><a class="h-[50px] flex items-center justify-center bg-[#F6F8FC] px-8 text-center" href="'. $item['url'] .'">' . $item['content'] . '</a></li>';
			}
			$output .= '</ul></div>';
		} else {
			$output .= '<p class="text-red-500">' . esc_html__('No items defined.', 'bricks') . '</p>';
		}
		echo $output;


		$this->element_script();
	}

	private function element_script() {
        // Try reusable scroll function
		?>
		<script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('announcement-container');
                const notifications = Array.from(container.children);
                const notificationHeight = notifications[0].clientHeight;
                let currentIndex = 0;
                let intervalId;

                // Thêm bản sao của thông báo đầu tiên và cuối cùng vào container
                const firstNotificationClone = notifications[0].cloneNode(true);
                const lastNotificationClone = notifications[notifications.length - 1].cloneNode(true);
                container.appendChild(firstNotificationClone);
                container.insertBefore(lastNotificationClone, notifications[0]);

                // Cập nhật lại danh sách notifications
                const updatedNotifications = Array.from(container.children);

                function startSlider() {
                    intervalId = setInterval(() => {
                        currentIndex++;
                        container.style.transition = 'transform 0.5s ease';
                        container.style.transform = `translateY(-${currentIndex * notificationHeight}px)`;

                        // Khi đến bản sao của thông báo đầu tiên
                        if (currentIndex === updatedNotifications.length - 1) {
                            setTimeout(() => {
                                container.style.transition = 'none';
                                container.style.transform = `translateY(-${notificationHeight}px)`;
                                currentIndex = 1;
                                // Re-enable the transition after resetting
                                setTimeout(() => {
                                    container.style.transition = '';
                                }, 50);
                            }, 500); // Thời gian trùng với transition để hoàn tất
                        }
                    }, 5000);
                }

                function stopSlider() {
                    clearInterval(intervalId);
                }

                container.addEventListener('mouseover', stopSlider);
                container.addEventListener('mouseout', startSlider);

                startSlider();

                // Hide announcement bar on scroll
                // const announcementBar = container.parentElement;
                // window.addEventListener('scroll', function() {
                //     if (window.scrollY > 20) {
                //         announcementBar.classList.add('opacity-0', 'hidden');
                //     } else {
                //         announcementBar.classList.remove('opacity-0', 'hidden');
                //     }
                // });
            });
		</script>

		<?php
	}
}