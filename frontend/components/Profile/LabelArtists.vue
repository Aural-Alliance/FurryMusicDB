<template>
    <loading :loading="isLoading">
        <h1>Artists in {{ label.name }}</h1>
    </loading>

    <div class="buttons">
        <router-link class="btn btn-primary" :to="{name: 'profile:label:artist:create', params: {
            label_id: labelId
        }}">
            <icon icon="plus-lg"/>
            <span>
                Create Artist Profile
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
        <template #cell(name)="{item}">
            <router-link :to="{
                name: 'artist',
                params: {
                    artist_id: item.id
                }
            }" class="text-big text-reset" target="_blank">
                {{ item.name }}
            </router-link>
        </template>
        <template #cell(actions)="{item}">
            <div class="btn-group btn-group-sm">
                <router-link class="btn btn-secondary"
                             :to="{name: 'profile:label:artist:edit', params: {
                                 'label_id': labelId, 'artist_id': item.id}
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
import {useInjectAxios} from "~/vendor/api.ts";
import Loading from "~/components/Common/Loading.vue";
import DataTable, {DataTableField} from "~/components/Common/DataTable.vue";
import Icon from "~/components/Common/Icon.vue";
import useConfirmAndDelete from "~/functions/useConfirmAndDelete.ts";
import {useAsyncState} from "@vueuse/core";

const {params} = useRoute();
const labelId = params.label_id;

const axios = useInjectAxios();

const {state: label, isLoading} = useAsyncState(
    () => axios.get(`/api/profile/label/${labelId}`).then(r => r.data),
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

const {state, isLoading: stateLoading, execute: relist} = useAsyncState(
    () => axios.get(`/api/profile/label/${labelId}/artists`).then(r => r.data),
    []
);

const {doDelete} = useConfirmAndDelete(
    'Delete artist?',
    () => {
        relist();
    }
)
</script>
