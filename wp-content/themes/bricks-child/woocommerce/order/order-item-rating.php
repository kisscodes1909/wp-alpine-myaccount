<?php
$rating = 3.5; // Giả sử rating là 4.5
?>
<div class="flex items-center space-x-1 flex-row gap-4 flex-wrap">
    <div class="flex space-x-1">
        <?php for($i = 1; $i <= 5; $i++): ?>
            <?php if($i <= $rating): ?>
                <!-- Colored star for the actual rating -->
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 36 34" fill="none">
                    <path d="M18 4L21.3677 14.3647H32.2658L23.4491 20.7705L26.8168 31.1353L18 24.7295L9.18322 31.1353L12.5509 20.7705L3.73415 14.3647H14.6323L18 4Z" fill="#CAA15F" stroke="#CAA15F" stroke-width="2"/>
                </svg>
            <?php elseif($i - 0.5 == $rating): ?>
                <!-- Half-colored star for the half rating -->
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 36 34" fill="none">
                    <path d="M18 4L21.3677 14.3647H32.2658L23.4491 20.7705L26.8168 31.1353L18 24.7295L9.18322 31.1353L12.5509 20.7705L3.73415 14.3647H14.6323L18 4Z" fill="url(#half_grad)" stroke="#CAA15F" stroke-width="2"/>
                    <defs>
                        <linearGradient id="half_grad" x1="0" x2="36" y1="0" y2="0" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#CAA15F"/>
                            <stop offset="0.5" stop-color="#CAA15F"/>
                            <stop offset="0.5" stop-color="white"/>
                            <stop stop-color="white"/>
                        </linearGradient>
                    </defs>
                </svg>
            <?php else: ?>
                <!-- Empty star for the remaining rating -->
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 36 34" fill="none">
                    <path d="M18 4L21.3677 14.3647H32.2658L23.4491 20.7705L26.8168 31.1353L18 24.7295L9.18322 31.1353L12.5509 20.7705L3.73415 14.3647H14.6323L18 4Z" fill="white" stroke="#CAA15F" stroke-width="2"/>
                </svg>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
</div>