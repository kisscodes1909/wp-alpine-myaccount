<?php
/**
 * Loader container – kept for DOM target only.
 * Loading is now in-button only (app-style): use isLoading/saving in components
 * and show spinner + "Saving..." inside the submit button.
 */
?>
<template x-data x-teleport="#loader-container">
    <div class="ma-ui-loader-placeholder" aria-hidden="true"></div>
</template>
