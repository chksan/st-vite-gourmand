<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
onMounted(() => {
  axios.get('/api/v1/me').then(res => {
    console.log('Logged in', res.data);
  }).catch(() => {
    console.log('Redirect');
  });
});
</script>