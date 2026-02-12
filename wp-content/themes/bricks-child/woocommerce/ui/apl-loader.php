<?php
/**
 * Loader container – kept for DOM target only.
 * Loading is now in-button only (app-style): use isLoading/saving in components
 * and show spinner + "Saving..." inside the submit button. See form-edit-account,
 * apl-form-edit-change-password, apl-form-edit-address.
 */
?>
<template x-data x-teleport="#loader-container">
    <!-- No full-page overlay; button-level loading only -->
</template>