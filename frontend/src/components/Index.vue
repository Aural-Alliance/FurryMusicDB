<template>
    <section class="hero is-fullheight-with-navbar">
        <div class="hero-body">
            <div class="container text-center">
                <p class="title">
                    Welcome to the Furry Music DB
                </p>
                <p class="subtitle">
                    Add yourself to the database!
                </p>
                <div class="button-wrapper">
                    <router-link v-if="isAuthenticated"
                                 to="/profile"
                                 class="btn btn-lg btn-primary"
                    >
                        Visit Profile
                    </router-link>
                    <button v-else
                            type="button"
                            class="btn btn-lg btn-primary"
                            @click.prevent="login"
                    >
                        Sign Up
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import {useAuth0} from "@auth0/auth0-vue";

const {
    isAuthenticated,
    loginWithRedirect,
} = useAuth0();

const login = () => {
    loginWithRedirect();
};
</script>
