<template>
    <section class="section">
        <template v-if="userLoading">
            <p>Loading...</p>
        </template>

        <template v-if="!userLoading">
            <div class="d-flex align-items-center mb-3">
                <div class="flex-shrink-0">
                    <img :src="user.avatar" style="width: 96px; height: 96px;" alt="Avatar">
                </div>
                <div class="flex-grow-1 ms-3">
                    <h1 class="title m-0">{{ user.name }}</h1>
                    <h2 class="subtitle m-0">{{ user.email }}</h2>
                </div>
            </div>
        </template>
    </section>

    <template v-if="labelsLoading || artistsLoading">
        <hr>
        <section class="section">
            <p>Loading profiles...</p>
        </section>
    </template>
    <template v-else-if="labels.length > 0 || artists.length > 0">
        <template v-if="labels.length > 0">
            <hr>
            <section class="section">
                <h2>My Label Profiles</h2>

                <div class="buttons">
                    <router-link class="btn btn-primary" :to="{name: 'profile:label:create'}">
                        <icon icon="plus-lg"/>
                        <span>
                        Create Another Label Profile
                    </span>
                    </router-link>
                </div>

                <data-table :fields="labelFields" :items="labels"
                            :loading="labelsLoading" @clickRefresh="refreshLabels" handle-client-side>
                    <template #cell(name)="{item}">
                        <router-link :to="{
                            name: 'label',
                            params: {
                                label_id: item.id
                            }
                        }" class="text-big text-reset" target="_blank">
                            {{ item.name }}
                        </router-link>
                    </template>
                    <template #cell(actions)="{item}">
                        <div class="btn-group btn-group-sm">
                            <router-link class="btn btn-primary"
                                         :to="{name: 'profile:label:artists', params: {'label_id': item.id}}"
                            >
                                <icon icon="people-fill"/>
                                <span>
                                Artists
                            </span>
                            </router-link>
                            <router-link class="btn btn-secondary"
                                         :to="{name: 'profile:label:edit', params: {'label_id': item.id}}"
                            >
                                <icon icon="pencil"/>
                                <span>
                                Edit
                            </span>
                            </router-link>
                            <button class="btn btn-danger"
                                    @click="doDeleteLabel(item.links.self)">
                                <icon icon="trash"/>
                                <span>
                                Delete
                            </span>
                            </button>
                        </div>
                    </template>
                </data-table>
            </section>
        </template>
        <template v-if="artists.length > 0">
            <hr>
            <section class="section">
                <h2>My Artist Profiles</h2>

                <div class="buttons">
                    <router-link class="btn btn-primary" :to="{name: 'profile:artist:create'}">
                        <icon icon="plus-lg"/>
                        <span>
                        Create Another Artist Profile
                    </span>
                    </router-link>
                </div>

                <data-table :fields="artistFields" :items="artists"
                            :loading="artistsLoading" @clickRefresh="refreshArtists" handle-client-side>
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
                                         :to="{name: 'profile:artist:edit', params: {'artist_id': item.id}}"
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
            </section>
        </template>
    </template>
    <template v-else>
        <hr>
        <section class="section">
            <h2>Create a New Profile</h2>

            <p>You haven't created an artist or label profile yet. If you want to submit an entry to the database as an
                artist or label,
                select the appropriate option below.</p>

            <div class="buttons">
                <router-link class="btn btn-primary" :to="{name: 'profile:label:create'}">
                    <icon icon="plus-lg"/>
                    <span>
                        Create Label Profile
                    </span>
                </router-link>
                <router-link class="btn btn-primary" :to="{name: 'profile:artist:create'}">
                    <icon icon="plus-lg"/>
                    <span>
                        Create Artist Profile
                    </span>
                </router-link>
            </div>
        </section>
    </template>
</template>

<script setup lang="ts">
import {useInjectAxios} from "~/vendor/api.ts";
import Icon from "~/components/Common/Icon.vue";
import DataTable, {DataTableField} from "~/components/Common/DataTable.vue";
import useConfirmAndDelete from "~/functions/useConfirmAndDelete.ts";
import {useAsyncState} from "@vueuse/core";
import {useUserStore} from "~/stores/user.ts";

const {user} = useUserStore();

const labelFields: DataTableField[] = [
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

const axios = useInjectAxios();

const {state: labels, isLoading: labelsLoading, execute: refreshLabels} = useAsyncState(
    () => axios.get('/profile/labels').then(r => r.data),
    []
);

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

const {state: artists, isLoading: artistsLoading, execute: refreshArtists} = useAsyncState(
    () => axios.get('/profile/artists').then(r => r.data),
    []
);

const {doDelete: doDeleteLabel} = useConfirmAndDelete(
    'Delete label profile?',
    () => {
        refreshLabels();
    }
);

const {doDelete: doDeleteArtist} = useConfirmAndDelete(
    'Delete artist profile?',
    () => {
        refreshArtists();
    }
);
</script>
