<template>
    <nav class="navbar navbar-expand-md" role="navigation" aria-label="main navigation">
        <div class="container">
            <router-link class="navbar-brand d-flex" to="/">
                <img src="/icon.svg" alt="Logo"/>
                <span class="logotype">
                    Furry Music<br>Database
                </span>
            </router-link>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#headerNavbar" aria-controls="headerNavbar"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="headerNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/about">About</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/labels">Labels</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/artists">Musicians</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/reviews">Reviews</router-link>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item" v-if="!isAuthenticated && !isLoading">
                        <a class="nav-link" href="#" @click.prevent="login">Sign In</a>
                    </li>
                    <li class="nav-item dropdown" v-if="isAuthenticated">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            {{ user.name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <router-link class="dropdown-item" to="/profile">My Profile</router-link>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" @click.prevent="logout">Sign Out</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/help">Help</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/donate">Donate</router-link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import {useAuth0} from "@auth0/auth0-vue";

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
