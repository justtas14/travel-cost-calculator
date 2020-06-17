require('./bootstrap');

window.Vue = require('vue');

Vue.component('app', require('./components/app.vue').default);

if(document.getElementById("app")){
    const app = new Vue({
        el: '#app',
    });
}
