<template>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <router-link class="navbar-item" to="/">
                <img src="/icon.svg" class="logo" alt="Logo"/>
                <span class="logotype">
                    Furry Music<br>Database
                </span>
            </router-link>

            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false"
               data-target="header-nav" @click.prevent="menuActive = !menuActive">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="header-nav" class="navbar-menu" :class="{ 'is-active': menuActive }">
            <div class="navbar-start">
                <router-link class="navbar-item" to="/about">About</router-link>
                <a class="navbar-item">Labels</a>
                <a class="navbar-item">Musicians</a>
                <a class="navbar-item">Reviews</a>
            </div>

            <div class="navbar-end">
                <template v-if="!isAuthenticated && !isLoading">
                    <a class="navbar-item" href="#" @click.prevent="login">Sign Up/Sign In</a>
                </template>
                <template v-if="isAuthenticated">
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link">
                            {{ user.name }}
                        </a>

                        <div class="navbar-dropdown">
                            <router-link class="navbar-item" to="/profile">My Profile</router-link>
                            <a class="navbar-item" href="#" @click.prevent="logout">Sign Out</a>
                        </div>
                    </div>
                </template>

                <router-link class="navbar-item" to="/help">Help</router-link>
                <router-link class="navbar-item" to="/donate">Donate</router-link>
            </div>
        </div>
    </nav>
</template>

<script setup>
import {useAuth0} from "@auth0/auth0-vue";
import {ref} from "vue";

const menuActive = ref(false);

const {
    isAuthenticated,
    isLoading,
    user,
    loginWithRedirect,
    logout: auth0Logout
} = useAuth0();

const login = () => {
    loginWithRedirect();
};
const logout = () => {
    auth0Logout({
        logoutParams: {
            returnTo: window.location.origin
        }
    });
};
</script>
