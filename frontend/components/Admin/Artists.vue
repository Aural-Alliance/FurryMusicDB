<template>
    <h1>Administer Artists</h1>

    <data-table :fields="artistFields" api-url="/api/admin/artists">
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
                             :to="{name: 'admin:artist:edit', params: {'artist_id': item.id}}"
                >
                    <icon icon="pencil"/>
                    <span>
                    Edit
                </span>
                </router-link>
                <button class="btn btn-danger"
                        @click="doDeleteArtist(item.links.self)">
                    <icon icon="trash"/>
                    <span>
                    Delete
                </span>
                </button>
            </div>
        </template>
    </data-table>
</template>

<script setup lang="ts">
import Icon from "~/components/Common/Icon.vue";
import DataTable, {DataTableField} from "~/components/Common/DataTable.vue";
import useConfirmAndDelete from "~/functions/useConfirmAndDelete.ts";

const artistFields: DataTableField[] = [
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

const {doDelete: doDeleteArtist} = useConfirmAndDelete(
    'Delete artist profile?',
    () => {
        refreshArtists();
    }
);
</script>
