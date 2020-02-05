import Axios from "axios"
import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util'

const state = {
  user: null,
  apiStatus: null,
  loginErrorMessages: null,
  registerErrorMessages: null,
}

const getters = {
  check: state => !! state.user,
  username: state => state.user ? state.user.name : ''
}

const mutations = {
  setUser (state, user) {
    state.user = user
  },
  setApiStatus (state, status) {
    state.apiStatus = status
  },
  setLoginErrorMessages (state, messages) {
    state.loginErrorMessages = messages
  },
  setRegisterErrorMessages (state, messages) {
    state.registerErrorMessages = messages
  }
}

const actions = {
  async register (context, data) {
    const response = await axios.post('/api/register', data)
    context.commit('setUser', response.data)

    if (response.status === CREATED) {
      context.commit('setApiStatus', true)
      context.commit('setUser', response.data)
      return false
    }

    context.commit('setApiStatus', false)
    if (response.status === UNPROCESSABLE_ENTITY) {
      context.commit('setRegisterErrorMessages',response.data.errors)
    } else {
      // 通信に失敗した場合に error モジュールの setCode ミューテーションを commit していますが、
      // あるストアモジュールから別のモジュールのミューテーションを commit する場合は
      // 第三引数に { root: true } を追加します。
      context.commit('error/setCode',response.status, { root: true })
    }
  },

  async login (context, data) {
    context.commit('setApiStatus', null)
    const response = await axios.post('/api/login', data)
      // TODO:.catch(err => err.response || err)

    // 200の場合
    if (response.state === OK) {
      context.commit('setApiStatus', true)
      context.commit('setUser', response.data)
      return false
    }

    context.commit('setApiStatus', false)
    // 422の場合
    if (response.status === UNPROCESSABLE_ENTITY) {
      context.commit('setLoginErrorMessages', response.data.errors)
    } else {
      context.commit('error/setCode', response.status, { root: true })
    }
    
  },

  async logout (context) {
    const response = await axios.post('/api/logout')

    if (response.status === OK) {
      context.commit('setApiStatus', true)
      context.commit('setUser', null)
      return false
    }

    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true })
  },

  async currentUser (context) {
    const response = await axios.get('/api/user')
    const user = response.data || null

    if (response.status === OK) {
      context.commit('setApiStatus', true)
      context.commit('setUser', user)
      return false
    }

    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true })
  }
}

export default {
  namespaced: true, //authをルートとして使用可能
  state,
  getters,
  mutations,
  actions
}
