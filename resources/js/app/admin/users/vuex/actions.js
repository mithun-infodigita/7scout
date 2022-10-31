export const fetchUsers= ({ commit }) => {
    return  axios.get(`/api/admin/groups`).then((response) => {
        commit('SET_USERS_DATA', response.data)
    })
}

export const fetchUser = ({ commit }, id) => {
    return  axios.get(`/api/admin/users/${id}`).then((response) => {
        commit('SET_USER_DATA', response.data)
    })
}

export const setUser = ({ state, commit }, data) => {
    commit('SET_USER_DATA', data)
}

export const setSelectedTab = ({ dispatch, state, commit}, value) => {
    commit('SET_SELECTED_TAB', value)
}

export const setToken = ({ state, commit }, data) => {
    commit('SET_TOKEN_DATA', data)
}
