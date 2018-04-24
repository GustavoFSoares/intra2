import Vue from 'vue'
import App from './App'

import VueResource from 'vue-resource'
import router from './routes'

import VueTheMask from 'vue-the-mask'
import VeeValidate, { Validator } from 'vee-validate';
import translate from 'vee-validate/dist/locale/pt_BR';
import Vuex from 'vuex'

import 'es6-promise/auto' //testar promise em IE11
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap/dist/js/bootstrap.js'
import 'font-awesome/css/font-awesome.css'

Vue.config.productionTip = false

Validator.localize('pt_BR', translate)
Vue.use(VueTheMask)
Vue.use(VeeValidate);
Vue.use(VueResource)
Vue.use(Vuex)
// Vue.use(BootstrapVue);

new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
