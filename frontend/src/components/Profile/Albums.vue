<template>
    <loading :loading="isLoading">
        <h1>{{ artist.name }} Albums</h1>
    </loading>

    <div class="buttons">
        <router-link class="btn btn-primary" :to="{name: 'profile:artist:album:create', params: {
            artist_id: artistId
        }}">
            <icon icon="plus-lg"/>
            <span>
                Add New Album
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
                <router-link class="btn btn-primary"
                             :to="{name: 'profile:artist:album:tracks', params: {
                                 'artist_id': artistId,
                                 'album_id': item.id
                             }
                }">
                    <icon icon="file-music-fill"/>
                    <span>Tracks</span>
                </router-link>
                <router-link class="btn btn-secondary"
                             :to="{name: 'profile:artist:album:edit', params: {
                                 'artist_id': artistId,
                                 'album_id': item.id
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

const {state: artist, isLoading} = getAuthenticatedResource(
    {
        url: `/profile/artist/${artistId}`,
        method: 'GET'
    },
    {
        name: null
    }
);

const fields: DataTableField[] = [
    {
        key: 'name',
        label: 'Name',
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
        url: `/profile/artist/${artistId}/albums`,
        method: 'GET'
    },
    []
);

const {doDelete} = useConfirmAndDelete(
    'Delete album?',
    () => {
        relist();
    }
)
</script>
