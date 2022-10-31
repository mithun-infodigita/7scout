export const fetchCustomsSettings = ({ commit }) => {
    return  axios.get(`/api/customsSettings`).then((response) => {
        commit('SET_CUSTOMS_SETTINGS_DATA', response.data)
    })
}

export const fetchCustomsSetting = ({ commit }, id) => {
    return  axios.get(`/api/customsSettings/${id}`).then((response) => {
        commit('SET_CUSTOMS_SETTING_DATA', response.data)
    })
}

export const setCustomsSetting = ({ state, commit }, data) => {
    commit('SET_CUSTOMS_SETTING_DATA', data)
}


