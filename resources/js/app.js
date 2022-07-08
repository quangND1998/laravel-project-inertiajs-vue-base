require('./bootstrap');

import Vue from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue'
import { InertiaProgress } from '@inertiajs/progress'
import PortalVue from 'portal-vue'
import InfiniteLoading from 'vue-infinite-loading';
import VueChatScroll from 'vue-chat-scroll'
import VueMapbox from "vue-mapbox";
import Mapbox from "mapbox-gl";
createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    setup({ el, App, props, plugin }) {
        Vue.use(plugin)

        new Vue({
            render: h => h(App, props),
        }).$mount(el)
    },
})
Vue.mixin({
    methods: {
        route: window.route,
    }
})
Vue.use(InfiniteLoading, {
    /* options */
});
Vue.use(VueChatScroll)
Vue.mixin(require('./base'))
Vue.use(PortalVue)


Vue.use(VueMapbox, { mapboxgl: Mapbox });
Vue.mixin({
    methods: {
        hasAnyPermission: function(permissions) {

            var allPermissions = this.$page.props.auth.can;
            var hasPermission = false;
            permissions.forEach(function(item) {
                if (allPermissions[item]) hasPermission = true;
            });
            return hasPermission;
        },
        formatDate: function(value) {
            if (value) {
                return moment(String(value)).format('DD/MM/YYYY HH:mm')
            }
        },

        formatDateMonth: function(value) {
            if (value) {
                return moment(String(value), "YYYY-MM-DD").format("MMM D YY")
            }
        },

    },
})
window.Bus = new Vue();
InertiaProgress.init({
    delay: 250,
    color: '#1E377F',
    includeCSS: true,
    showSpinner: true,
});