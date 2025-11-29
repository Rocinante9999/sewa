import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Ziggy } from './ziggy'; // Mengarah ke file ziggy.js di folder yang sama
import Alpine from 'alpinejs';

// PERBAIKAN: Gunakan nama aplikasi Anda sebagai fallback
const appName = import.meta.env.VITE_APP_NAME || 'Sewa Instan';

createInertiaApp({
  // Gunakan appName yang sudah didefinisikan
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, window.Ziggy)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});

window.Alpine = Alpine;
Alpine.start();

