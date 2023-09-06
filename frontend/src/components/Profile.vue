<template>
    <section class="section">
        <template v-if="userLoading">
            <p>Loading...</p>
        </template>

        <template v-if="!userLoading">
            <article class="media mb-3">
                <div class="media-left">
                    <figure class="image is-128x128">
                        <img :src="localUser.avatar" alt="Avatar">
                    </figure>
                </div>
                <div class="media-content">
                    <div class="content">
                        <h1 class="title">{{ localUser.name }}</h1>
                        <h2 class="subtitle">
                            <a :href="`mailto:${localUser.email}`">
                                {{ localUser.email }}
                            </a>
                        </h2>
                    </div>
                </div>
            </article>
        </template>
    </section>

    <section class="section">
        <template v-if="labelsLoading || artistsLoading">
            <p>Loading profiles...</p>
        </template>
        <template v-else-if="labels.length > 0">
            <h2>My Label Profiles</h2>

            <div class="buttons">

            </div>
        </template>
        <template v-else-if="artists.length > 0">
            <h2>My Artist Profiles</h2>

            <div class="buttons">

            </div>


        </template>
        <template v-else>
            <h2>Create a New Profile</h2>

            <p>You haven't created an artist or label profile yet. If you want to submit a new artist or label,
                select the appropriate option below.</p>

            <div class="buttons">
                
            </div>
        </template>
    </section>
</template>

<script setup>
import {useAuth0} from "@auth0/auth0-vue";
import {getAuthenticatedResource} from "../api.js";

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

const {state: labels, isLoading: labelsLoading} = getAuthenticatedResource(
    {
        url: '/labels',
        method: 'GET'
    }, []
);

const {state: artists, isLoading: artistsLoading} = getAuthenticatedResource(
    {
        url: '/artists',
        method: 'GET'
    }, []
);
</script>
