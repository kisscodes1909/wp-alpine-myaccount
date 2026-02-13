<div class="my-8 md:my-20 md:container mx-auto px-8 relative">
    <?php
        if(isset($prev_page)) {
            ?>
                <a class="flex flex-row items-center relative md:absolute top-0 left-[-10px] md:left-[13px]" href="<?php echo $prev_page['url'] ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="scale-75 md:scale-100 w-[25px] h-[26px]" viewBox="0 0 25 26" fill="none">
                        <path d="M14.5842 6.5L9.07072 12.234C8.66393 12.657 8.66393 13.343 9.07072 13.766L14.5842 19.5" stroke="#4D4D4D" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span class="text-sm md:text-base"><?php echo $prev_page['title'] ?></span>
                </a>
            <?php
        }
    ?>
    <h2 class="apl-heading-chip uppercase"><?php echo $page_heading ?? ''; ?></h2>
    <?php if (!empty($page_description)) : ?>
        <p class="text-sm pb-4 border-b border-gray-200"><?php echo $page_description; ?></p>
    <?php endif; ?>
</div>
