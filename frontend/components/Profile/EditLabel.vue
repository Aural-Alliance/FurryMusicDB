<template>
    <h1>{{ meta.title }}</h1>

    <listing-form :api-url="apiUrl" :is-edit-mode="isEditMode" listing-type="label" @submitted="onSubmitted"/>
</template>

<script setup lang="ts">
import {useRoute, useRouter} from "vue-router";
import {computed} from "vue";
import ListingForm from "~/components/Profile/ListingForm.vue";

const {meta, params} = useRoute();
const isEditMode = 'label_id' in params;

const apiUrl = computed(() => {
    return (isEditMode)
        ? `/api/profile/label/${params.label_id}`
        : `/api/profile/labels`;
});

const router = useRouter();
const onSubmitted = async () => {
    await router.push({name: 'profile'});
};
</script>
