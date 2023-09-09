<template>
    <navbar/>

    <div class="container">
        <breadcrumbs/>

        <router-view/>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3">
    </div>
</template>

<script setup lang="ts">
import Navbar from "./components/Navbar.vue";
import Breadcrumbs from "~/components/Common/Breadcrumbs.vue";
</script>
