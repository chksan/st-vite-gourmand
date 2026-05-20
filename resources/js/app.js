import './bootstrap';
import { createApp } from 'vue';
import App from './components/app.vue';
import router from './Router';

const app = createApp(App);
app.use(router);
app.mount('#app');