<template>
    <section class="section">
        <h1 class="title">Help</h1>
        <h2 class="subtitle">
            Subtitle
        </h2>

        <p>
            Coming Soon!
        </p>

        <template v-if="!userLoading">
            {{ localUser }}
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
        name: null
    }
)
</script>
