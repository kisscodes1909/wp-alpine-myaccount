<div class="ma-page-heading">
    <?php
        if ( ! empty( $prev_page ) && ! empty( $prev_page['url'] ) ) {
            ?>
                <a class="ma-page-heading__back-link" href="<?php echo esc_url( $prev_page['url'] ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ma-page-heading__back-icon" viewBox="0 0 25 26" fill="none" aria-hidden="true">
                        <path d="M14.5842 6.5L9.07072 12.234C8.66393 12.657 8.66393 13.343 9.07072 13.766L14.5842 19.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span class="ma-page-heading__back-label"><?php echo esc_html( $prev_page['title'] ); ?></span>
                </a>
            <?php
        }
    ?>
    <?php if ( ! empty( $page_heading ) ) : ?>
    <div class="ma-page-heading__title-row">
        <?php
        $heading_icon = isset( $page_heading_icon ) ? $page_heading_icon : '';
        if ( 'order' === $heading_icon || 'document' === $heading_icon ) :
            ?>
        <span class="ma-page-heading__lead-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
        </span>
            <?php
        endif;
        ?>
        <h2 class="ma-page-heading__title"><?php echo esc_html( $page_heading ); ?></h2>
    </div>
    <?php endif; ?>
    <?php if ( ! empty( $page_description ) ) : ?>
        <p class="ma-page-heading__description"><?php echo esc_html( $page_description ); ?></p>
    <?php endif; ?>
</div>
