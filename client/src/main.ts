
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import '@/assets/styles.css';
import Toast from "vue-toastification";
// Import the CSS or use your own!
import "vue-toastification/dist/index.css";
import App from './App.vue'

const app = createApp(App)

app.use(createPinia())
app.use(Toast, {})

app.mount('#app')
