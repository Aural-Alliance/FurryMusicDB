<template>
    <div class="container">
        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="https://bulma.io">
                    <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28">
                </a>

                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false"
                   data-target="navbarBasicExample">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>

            <div id="navbarBasicExample" class="navbar-menu">
                <div class="navbar-start">
                    <a class="navbar-item">About</a>
                    <a class="navbar-item">Labels</a>
                    <a class="navbar-item">Musicians</a>
                    <a class="navbar-item">Reviews</a>
                </div>

                <div class="navbar-end">
                    <a class="navbar-item">Sign Up/Sign In</a>
                    <a class="navbar-item">Help</a>
                    <a class="navbar-item">Donate</a>
                </div>
            </div>
        </nav>

        <router-view/>
    </div>
</template>

<script setup></script>
