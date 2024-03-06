<template>
    <loading :loading="isLoading" lazy>
        <div class="d-flex mb-3">
            <div class="flex-shrink-0">
                <avatar type="artist" class="lg" :id="state.id" :art-updated-at="state.art_updated_at"/>
            </div>
            <div class="flex-grow-1 ms-3">
                <h1>{{ state.name }}</h1>
                <h6>Submitted {{ formatTimestamp(state.created_at) }} &bull; Updated
                    {{ formatTimestamp(state.updated_at) }}</h6>
                <h4>{{ state.description }}</h4>

                <social-links :socials="state.socials"/>
            </div>
        </div>
    </loading>
</template>

<script setup lang="ts">
import {useRoute} from "vue-router";
import {useAsyncState} from "@vueuse/core";
import {useInjectAxios} from "~/vendor/api.ts";
import Loading from "~/components/Common/Loading.vue";
import Avatar from "~/components/Common/Avatar.vue";
import {useLuxon} from "~/vendor/luxon.ts";
import SocialLinks from "~/components/SocialLinks.vue";

const {params} = useRoute();
const artistId = params.artist_id;

const axios = useInjectAxios();
const {state, isLoading} = useAsyncState(
    () => axios.get(`/api/artist/${artistId}`).then(r => r.data),
    {
        id: null,
        name: null
    }
);

const {DateTime, timestampToDateTime} = useLuxon();
const formatTimestamp = (datetime: string | number): string => {
    return timestampToDateTime(datetime).toLocaleString(DateTime.DATE_MED);
}
</script>
