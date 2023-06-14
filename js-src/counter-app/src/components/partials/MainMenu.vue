<script setup>
import { RouterLink } from 'vue-router'
import { useUserStore } from '@/stores/user';

const userStore = useUserStore();
</script>
<template>
    <nav>
        <ul>
            <li class="left">
                <RouterLink to="/">Home</RouterLink>
            </li>
            <li class="right">
                <RouterLink to="/login" v-if="!userStore.user">Login</RouterLink>
                <RouterLink to="/login" v-if="userStore.user" @click="userStore.logout">Logout</RouterLink>
            </li>
        </ul>
    </nav>
</template>
<style scoped>
nav ul{
    padding:0;
}
nav li{
    list-style-type: none;
    padding: 2px 5px;
}

nav li.right{
    float:right
}

nav li.left{
    float:left
}
</style>