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
                <router-link v-if="isLoggedIn"
                             to="/profile"
                             class="btn btn-lg btn-primary"
                >
                    Visit Profile
                </router-link>
                <a v-else
                   class="btn btn-lg btn-primary"
                   href="/api/login"
                >
                    Sign Up
                </a>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import {useUserStore} from "~/stores/user.ts";

const {
    isLoggedIn
} = useUserStore();
</script>
