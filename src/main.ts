import Vue from 'vue';
import App from './App.vue';
import router from './router';
import NutUI from '@nutui/nutui';
import '@nutui/nutui/dist/nutui.css';

import './registerServiceWorker';

Vue.config.productionTip = false;
NutUI.install(Vue, {});

new Vue({
    router,
    render: (h) => h(App),
}).$mount('#app');
