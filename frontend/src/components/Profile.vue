<template>
    <section class="section">
        <template v-if="userLoading">
            <p>Loading...</p>
        </template>

        <template v-if="!userLoading">
            <div class="d-flex align-items-center mb-3">
                <div class="flex-shrink-0">
                    <img :src="localUser.avatar" style="width: 96px; height: 96px;" alt="Avatar">
                </div>
                <div class="flex-grow-1 ms-3">
                    <h1 class="title m-0">{{ localUser.name }}</h1>
                    <h2 class="subtitle m-0">
                        <a :href="`mailto:${localUser.email}`">
                            {{ localUser.email }}
                        </a>
                    </h2>
                </div>
            </div>
        </template>
    </section>

    <section class="section">
        <template v-if="labelsLoading || artistsLoading">
            <p>Loading profiles...</p>
        </template>
        <template v-else-if="labels.length > 0">
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
                                @click="doDelete(item.links.self)">
                            <icon icon="trash"/>
                            <span>
                                Delete
                            </span>
                        </button>
                    </div>
                </template>
            </data-table>
        </template>
        <template v-else-if="artists.length > 0">
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
                <template #cell(actions)="{item}">
                    <div class="btn-group btn-group-sm">
                        <router-link class="btn btn-primary"
                                     :to="{name: 'profile:artist:albums', params: {
                                 'artist_id': item.id
                             }
                        }">
                            <icon icon="folder"/>
                            <span>Albums</span>
                        </router-link>
                        <router-link class="btn btn-secondary"
                                     :to="{name: 'profile:artist:edit', params: {'artist_id': item.id}}"
                        >
                            <icon icon="pencil"/>
                            <span>
                                Edit
                            </span>
                        </router-link>
                        <button class="btn btn-danger"
                                @click="doDelete(item.links.self)">
                            <icon icon="trash"/>
                            <span>
                                Delete
                            </span>
                        </button>
                    </div>
                </template>
            </data-table>
        </template>
        <template v-else>
            <h2>Create a New Profile</h2>

            <p>You haven't created an artist or label profile yet. If you want to submit a new artist or label,
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
        </template>
    </section>
</template>

<script setup lang="ts">
import {useAuth0} from "@auth0/auth0-vue";
import {getAuthenticatedResource} from "~/vendor/api.ts";
import Icon from "~/components/Common/Icon.vue";
import DataTable, {DataTableField} from "~/components/Common/DataTable.vue";
import useConfirmAndDelete from "~/functions/useConfirmAndDelete.ts";

const {user} = useAuth0();

const {state: localUser, isLoading: userLoading} = getAuthenticatedResource(
    {
        url: '/users/me',
        method: 'GET'
    }, {
        id: null,
        email: null,
        name: null,
        avatar: null,
        updatedAt: 0
    }
);

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

const {state: labels, isLoading: labelsLoading, execute: refreshLabels} = getAuthenticatedResource(
    {
        url: '/profile/labels',
        method: 'GET'
    }, []
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

const {state: artists, isLoading: artistsLoading, execute: refreshArtists} = getAuthenticatedResource(
    {
        url: '/profile/artists',
        method: 'GET'
    }, []
);

const {doDelete} = useConfirmAndDelete(
    'Delete record?',
    () => {
        refreshLabels();
        refreshArtists();
    }
);
</script>
