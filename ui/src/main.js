import Vue from 'vue'

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/en'
Vue.use(ElementUI, { locale })

import App from './App.vue'
import router from './router'

require('./assets/fontawesome/css/fa-svg-with-js.css');
require('./assets/fontawesome/js/fontawesome-all.min.js');

require('./assets/bootstrap-grid.min.css')

Vue.prototype.isFPPremium = flopQ.isP;
Vue.prototype.flopQ = flopQ;
Vue.prototype.getFloPressPro = {
    goInternal: function(){ window.location.href=flopQ.gproi;},
    goExternal: function(){ window.open(flopQ.gproe); },
}

import { i18n } from './i18n.js'
new Vue({
  el: '#app',
  router,
  i18n,
  render: h => h(App)
})