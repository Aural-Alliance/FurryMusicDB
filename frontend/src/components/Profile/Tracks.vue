<template>
    <loading :loading="isLoading">
        <h1>Tracks in Album: {{ album.name }}</h1>
    </loading>

    <div class="buttons">
        <router-link class="btn btn-primary" :to="{name: 'profile:artist:album:track:create', params: {
            artist_id: artistId,
            album_id: albumId
        }}">
            <icon icon="plus-lg"/>
            <span>
                Add New Track
            </span>
        </router-link>
    </div>

    <data-table
        ref="$datatable"
        :fields="fields"
        :items="state"
        :loading="stateLoading"
        @clickRefresh="relist"
        handle-client-side
    >
        <template #cell(actions)="{item}">
            <div class="btn-group btn-group-sm">
                <router-link class="btn btn-secondary"
                             :to="{name: 'profile:artist:album:track:create', params: {
                                 'artist_id': artistId,
                                 'album_id': albumId,
                                 'track_id': item.id
                             }
                }">
                    <icon icon="pencil"/>
                    <span>Edit</span>
                </router-link>
                <button class="btn btn-danger"
                        @click="doDelete(item.links.self)">
                    <icon icon="trash"/>
                    <span>Delete</span>
                </button>
            </div>
        </template>
    </data-table>
</template>

<script setup lang="ts">
import {useRoute} from "vue-router";
import {getAuthenticatedResource} from "~/vendor/api.ts";
import Loading from "~/components/Common/Loading.vue";
import DataTable, {DataTableField} from "~/components/Common/DataTable.vue";
import Icon from "~/components/Common/Icon.vue";
import useConfirmAndDelete from "~/functions/useConfirmAndDelete.ts";

const {params} = useRoute();
const artistId = params.artist_id;
const albumId = params.album_id;

const {state: album, isLoading} = getAuthenticatedResource(
    {
        url: `/profile/artist/${artistId}/album/${albumId}`,
        method: 'GET'
    },
    {
        name: null
    }
);

const fields: DataTableField[] = [
    {
        key: 'title',
        label: 'Title',
        sortable: true,
    },
    {
        key: 'actions',
        label: 'Actions',
        class: 'shrink'
    }
];

const {state, isLoading: stateLoading, execute: relist} = getAuthenticatedResource(
    {
        url: `/profile/artist/${artistId}/album/${albumId}/tracks`,
        method: 'GET'
    },
    []
);

const {doDelete} = useConfirmAndDelete(
    'Delete track?',
    () => {
        relist();
    }
)
</script>
