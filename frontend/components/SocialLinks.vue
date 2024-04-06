<template>
    <dl class="row">
        <social-link v-for="row in socials" :key="row.id" :row="row"/>
    </dl>
</template>

<script setup lang="ts">
import SocialLink from "~/components/SocialLink.vue";
import {Social} from "~/entities/Socials.ts";

const props = defineProps<{
    socials: Social[]
}>();
</script>
