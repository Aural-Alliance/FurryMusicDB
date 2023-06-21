<template>
    <div class="container">
        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <router-link class="navbar-item" to="/">
                    <img src="/icon.svg" class="logo" alt="Logo"/>
                    <span class="logotype">
                        Furry Music<br>Database
                    </span>
                </router-link>

                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false"
                   data-target="navbarBasicExample">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>

            <div id="navbarBasicExample" class="navbar-menu">
                <div class="navbar-start">
                    <router-link class="navbar-item" to="/about">About</router-link>
                    <a class="navbar-item">Labels</a>
                    <a class="navbar-item">Musicians</a>
                    <a class="navbar-item">Reviews</a>
                </div>

                <div class="navbar-end">
                    <a class="navbar-item">Sign Up/Sign In</a>
                    <router-link class="navbar-item" to="/help">Help</router-link>
                    <router-link class="navbar-item" to="/donate">Donate</router-link>
                </div>
            </div>
        </nav>

        <router-view/>
    </div>
</template>

<script setup>
</script>
