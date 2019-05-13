import Vue from 'vue';
import App from './app.vue';
import router from './router';
import './registerServiceWorker';

Vue.config.productionTip = false;

new Vue({
    render: h => h(App)
}).$mount('#app');
