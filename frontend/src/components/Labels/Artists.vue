<template>
    <loading :loading="isLoading">
        <h1>Artists in {{ label.name }}</h1>
    </loading>

    <div class="buttons">
        <router-link class="btn btn-primary" :to="{name: 'label:artist:create', params: {
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
        :api-url="`/label/${labelId}/artists`"
    >
        <template #cell(actions)="{item}">
            <div class="btn-group btn-group-sm">
                <router-link class="btn btn-primary"
                             :to="{name: 'label:artist:edit', params: {
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
import {getAuthenticatedResource} from "~/vendor/api.ts";
import Loading from "~/components/Common/Loading.vue";
import DataTable from "~/components/Common/DataTable.vue";
import Icon from "~/components/Common/Icon.vue";
import useConfirmAndDelete from "~/functions/useConfirmAndDelete.ts";
import useHasDatatable, {DataTableTemplateRef} from "~/functions/useHasDatatable.ts";
import {ref} from "vue";

const {params} = useRoute();
const labelId = params.label_id;

const {state: label, isLoading} = getAuthenticatedResource(
    {
        url: `/label/${labelId}`,
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

const $datatable = ref<DataTableTemplateRef>(null);
const {relist} = useHasDatatable($datatable);

const {doDelete} = useConfirmAndDelete(
    'Delete artist?',
    () => {
        relist();
    }
)
</script>
