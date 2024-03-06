<template>
    <img class="avatar" alt="Album Art" :src="imgSrc"/>
</template>

<script setup lang="ts">
import {computed} from "vue";

const props = withDefaults(
    defineProps<{
        type: string,
        id: string,
        artUpdatedAt?: Number | null
    }>(), {
        artUpdatedAt: null
    }
);

const imgSrc = computed<string>(() => {
    if (props.artUpdatedAt === null) {
        return '/public/avatar.jpg';
    }

    return `/api/${props.type}/${props.id}/art-${props.artUpdatedAt}.jpg`;
});
</script>
