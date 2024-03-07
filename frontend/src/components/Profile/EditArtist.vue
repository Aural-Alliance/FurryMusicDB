<template>
    <h1>{{ meta.title }}</h1>

    <listing-form :api-url="apiUrl" :is-edit-mode="isEditMode" listing-type="artist" @submitted="onSubmitted"/>
</template>

<script setup lang="ts">
import {useRoute, useRouter} from "vue-router";
import {computed} from "vue";
import ListingForm from "~/components/Profile/ListingForm.vue";

const {meta, params} = useRoute();
const isEditMode = 'artist_id' in params;

const apiUrl = computed(() => {
    const prefix = ('label_id' in params)
        ? `/api/profile/label/${params.label_id}`
        : '/api/profile';

    return (isEditMode)
        ? `${prefix}/artist/${params.artist_id}`
        : `${prefix}/artists`;
});

const router = useRouter();
const onSubmitted = async () => {
    if ('label_id' in params) {
        await router.push({
            name: 'profile:label:artists',
            params: {
                label_id: params.label_id
            }
        });
    } else {
        await router.push({name: 'profile'});
    }
};
</script>
