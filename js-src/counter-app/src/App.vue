<script setup>
import { RouterView } from 'vue-router'
import MainMenu from './components/partials/MainMenu.vue'
</script>

<template>
  <menu>

    <MainMenu />

  </menu>

  <section>

    <RouterView />
  </section>
</template>

<style scoped>
menu{
  position:fixed;
  top:0;
  left:0;
  right:0;
  padding:5px 20px;
  height:40px;
  background-color: var(--color-menu-background-soft);
}

section{
  margin-top: 40px;
}
</style>
