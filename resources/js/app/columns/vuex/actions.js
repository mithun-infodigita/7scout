export const fetchColumns = ({ commit }) => {
    return  axios.get(`/api/columns`).then((response) => {
        commit('SET_COLUMNS_DATA', response.data)
    })
}

export const fetchColumn = ({ commit }, id) => {
    return  axios.get(`/api/columns/${id}`).then((response) => {
        commit('SET_COLUMN_DATA', response.data)
    })
}

export const setColumn = ({ state, commit }, data) => {
    commit('SET_COLUMN_DATA', data)
}


export const updateColumnsOrder = ({ dispatch, state, commit }, orderIds) => {
    return axios.patch(`/api/columns/updateOrder`, orderIds).then((response) => {
    }).catch((error) => {

    })
}

export const updateGroupColumns = ({ dispatch, state, commit }) => {
    return axios.get(`/api/columns/updateGroupColumns`).then((response) => {

    }).catch((error) => {

    })
}
