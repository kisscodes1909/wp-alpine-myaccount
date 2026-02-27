<div class="ma-page-heading">
    <?php
        if ( ! empty( $prev_page ) && ! empty( $prev_page['url'] ) ) {
            ?>
                <a class="ma-page-heading__back-link" href="<?php echo esc_url( $prev_page['url'] ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ma-page-heading__back-icon" viewBox="0 0 25 26" fill="none" aria-hidden="true">
                        <path d="M14.5842 6.5L9.07072 12.234C8.66393 12.657 8.66393 13.343 9.07072 13.766L14.5842 19.5" stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span class="ma-page-heading__back-label"><?php echo esc_html( $prev_page['title'] ); ?></span>
                </a>
            <?php
        }
    ?>
    <h2 class="apl-heading-chip ma-page-heading__title"><?php echo esc_html( $page_heading ?? '' ); ?></h2>
    <?php if ( ! empty( $page_description ) ) : ?>
        <p class="ma-page-heading__description"><?php echo esc_html( $page_description ); ?></p>
    <?php endif; ?>
</div>
