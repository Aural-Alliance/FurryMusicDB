<template>
    <section class="hero py-5">
        <div class="hero-body text-center">
            <h1 class="title">
                Welcome to the Furry Music Database
            </h1>
            <h2 class="subtitle">
                Discover new artists or add yourself to the database!
            </h2>
            <div class="buttons mt-5">
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
