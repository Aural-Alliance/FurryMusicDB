<template>
    <img class="avatar" alt="Album Art" :src="imgSrc"/>
</template>

<script setup lang="ts">
import {computed} from "vue";

const props = withDefaults(
    defineProps<{
        type: string,
        id: string,
        artUpdatedAt?: number
    }>(), {
        artUpdatedAt: 0
    }
);

const imgSrc = computed<string>(() => {
    return (props.artUpdatedAt > 0)
        ? `/api/${type}/${id}/art-${artUpdatedAt}.jpg`
        : '/public/avatar.jpg'
});
</script>
