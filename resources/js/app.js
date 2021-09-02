require('./bootstrap');
require('./fontawesome');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import Vuelidate from 'vuelidate';
import VueTailwind from 'vue-tailwind';
import Multiselect from 'vue-multiselect';
import VueMoment from 'vue-moment';

Vue.config.productionTip = false;
Vue.mixin({ methods: { route: window.route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(Vuelidate);
Vue.use(VueTailwind);
Vue.use(VueMoment);
Vue.component('multiselect', Multiselect);

const app = document.getElementById('app');

new Vue({
    render: h =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: name => require(`./Pages/${name}`).default
            }
        })
}).$mount(app);
