<template>
  <div>
    <Navbar />
    <router-view />
  </div>
</template>

<script setup>
import Navbar from '../components/Navbar.vue';
import { useAuth } from '../Helpers/auth';
import { onMounted } from 'vue';
</script>