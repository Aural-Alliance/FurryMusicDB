<template>
    <loading :loading="isLoading">
        <div class="d-flex mb-3">
            <div class="flex-shrink-0">
                <avatar type="label" class="lg" :id="state.id" :art-updated-at="state.art_updated_at"/>
            </div>
            <div class="flex-grow-1 ms-3">
                <h1>{{ state.name }}</h1>
                <h6>Submitted {{ formatIso(state.created_at) }} &bull; Updated {{ formatIso(state.updated_at) }}</h6>
                <h4>{{ state.description }}</h4>
            </div>
        </div>
        <hr>
    </loading>
</template>

<script setup lang="ts">
import {useRoute} from "vue-router";
import {useAsyncState} from "@vueuse/core";
import {useInjectAxiosPublic} from "~/vendor/api.ts";
import Loading from "~/components/Common/Loading.vue";
import Avatar from "~/components/Common/Avatar.vue";
import {useLuxon} from "~/vendor/luxon.ts";

const {params} = useRoute();
const labelId = params.label_id;

const axios = useInjectAxiosPublic();
const {state, isLoading} = useAsyncState(
    () => axios.get(`/label/${labelId}`).then(r => r.data),
    {
        id: null,
        name: null
    }
);

const {DateTime} = useLuxon();
const formatIso = (datetime: string): string => {
    return DateTime.fromISO(datetime).toLocaleString(DateTime.DATE_MED);
}
</script>
