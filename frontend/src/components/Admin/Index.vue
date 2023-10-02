<template>
    <h1>Administration</h1>

    <h2>Administer Content</h2>
    <div class="buttons">
        <router-link to="/admin/artists" class="btn btn-lg btn-primary">
            Artists
        </router-link>
        <router-link to="/admin/labels" class="btn btn-lg btn-primary">
            Labels
        </router-link>
    </div>
    <hr>
    <h2>Administer Users</h2>
    <p>Users are managed via the Authentik service.</p>
    <div class="buttons">
        <a class="btn btn-secondary" target="_blank" href="">
            Manage Users
        </a>
    </div>
</template>
