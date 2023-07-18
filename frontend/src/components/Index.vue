<template>
    <section class="hero is-fullheight-with-navbar">
        <div class="hero-body">
            <div class="container has-text-centered">
                <p class="title">
                    Welcome to the Furry Music DB
                </p>
                <p class="subtitle">
                    Add yourself to the database!
                </p>
                <div class="button-wrapper">
                    <router-link v-if="isAuthenticated"
                                 to="/profile"
                                 class="button is-primary is-large"
                    >
                        Visit Profile
                    </router-link>
                    <button v-else
                            type="button"
                            class="button is-primary is-large"
                            @click.prevent="login"
                    >
                        Sign Up
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import {useAuth0} from "@auth0/auth0-vue";

const {
    isAuthenticated,
    loginWithRedirect,
} = useAuth0();

const login = () => {
    loginWithRedirect();
};
</script>
