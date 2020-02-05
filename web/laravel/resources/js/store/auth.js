import Axios from "axios"

const state = {
  user: null
}
const getters = {
  check: state => !! state.user,
  username: state => state.user ? state.user.name : ''
}
const mutations = {
  setUser (state, user) {
    state.user = user
  }
}
const actions = {
  async register (context, data) {
    const response = await axios.post('/api/register', data)
    context.commit('setUser', response.data)
  }
}

export default {
  namespaced: true, //authをルートとして使用可能
  state,
  getters,
  mutations,
  actions
}
