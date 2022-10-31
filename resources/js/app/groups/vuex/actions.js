export const fetchGroups = ({ commit }) => {
    return  axios.get(`/api/groups`).then((response) => {
        commit('SET_GROUPS_DATA', response.data)
    })
}

export const fetchGroup = ({ commit }, id) => {
    return  axios.get(`/api/groups/${id}`).then((response) => {
        commit('SET_GROUP_DATA', response.data)
    })
}

export const setGroup = ({ state, commit }, data) => {
    commit('SET_GROUP_DATA', data)
}

export const setSelectedTab = ({ dispatch, state, commit}, value) => {
    commit('SET_SELECTED_TAB', value)
}

export const fetchGroupColumns = ({ commit }, groupId) => {
    return  axios.get(`/api/groups/${groupId}/groupColumns`).then((response) => {
        commit('SET_GROUP_COLUMNS_DATA', response.data)
    })
}

export const fetchGroupColumn = ({ commit }, {groupId, groupColumnId}) => {
    return  axios.get(`/api/groups/${groupId}/groupColumns/${groupColumnId}`).then((response) => {
        commit('SET_GROUP_COLUMN_DATA', response.data)
    })
}

export const setGroupColumn = ({ state, commit }, data) => {
    commit('SET_GROUP_COLUMN_DATA', data)
}

export const updateGroupColumnsOrder = ({ dispatch, state, commit }, {groupId, orderIds}) => {
    return axios.patch(`/api/groups/${groupId}/groupColumns/updateOrder`, {orderIds:orderIds}).then((response) => {
    }).catch((error) => {

    })
}
