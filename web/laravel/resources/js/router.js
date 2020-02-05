import Vue from 'vue'
import VueRouter from 'vue-router'

import PhotoList from './pages/PhotoList.vue'
import Login from  './pages/Login.vue'

import store from './store'

Vue.use(VueRouter)

const routes = [
  {
    path: '/login', component: Login,
    beforeEnter (to, from, next) {
      //checkした結果がnullの場合、トップページに遷移
      if (store.getters['auth/check']) {
        next('/')
      } else {
        next()
      }
    }
  }
]

const router = new VueRouter({
  mode: 'history',
  routes
})

export default router