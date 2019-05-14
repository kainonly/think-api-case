import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [{
        path: '/',
        name: 'service',
        component: () => import('./views/Service.vue'),
    }, {
        path: '/common',
        name: 'common',
        component: () => import('./views/Common.vue'),
    }],
});
