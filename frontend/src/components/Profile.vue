<template>
    <section class="section">
        <template v-if="userLoading">
            Loading...
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
</template>

<script setup>
import {useAuth0} from "@auth0/auth0-vue";
import {getAuthenticatedResource} from "../api.js";

const {user} = useAuth0();

const {state: localUser, isLoading: userLoading} = getAuthenticatedResource(
    {
        'url': '/users/me',
        'method': 'GET'
    }, {
        id: null,
        email: null,
        name: null,
        avatar: null,
        updatedAt: 0
    }
)
</script>
