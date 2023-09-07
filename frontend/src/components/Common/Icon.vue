<template>
    <i class="bi" :class="'bi-'+icon"></i>
</template>

<script setup lang="ts">
const props = defineProps({
    icon: {
        type: String,
        required: true
    }
});
</script>
