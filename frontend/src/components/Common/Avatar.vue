<template>
    <img class="avatar" alt="Album Art" :src="imgSrc"/>
</template>

<script setup lang="ts">
import {computed} from "vue";
import {useLuxon} from "~/vendor/luxon.ts";

const props = withDefaults(
    defineProps<{
        type: string,
        id: string,
        artUpdatedAt?: string | null
    }>(), {
        artUpdatedAt: null
    }
);

const {isoToTimestamp} = useLuxon();

const imgSrc = computed<string>(() => {
    if (props.artUpdatedAt === null) {
        return '/public/avatar.jpg';
    }

    const timestamp = isoToTimestamp(props.artUpdatedAt);
    return `/api/${type}/${id}/art-${timestamp}.jpg`;
});
</script>
