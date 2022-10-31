import Bus from "../../../../bus";

export const fetchTableFilters = ({ commit }) => {
    return axios.get('/api/tableFilters').then((response) => {
        commit('SET_TABLE_FILTERS_DATA', response.data)
    })
}

export const store = ({ dispatch, state, commit }, { payload, context }) => {
    return axios.post('/api/tableFilters', payload).then((response) => {
        Bus.$emit('showAlert', {color: 'success', 'message': 'Filter erfolgreich gespeichert' });
        commit('SET_ACTIVE_FILTER_DATA', response.data)
        dispatch('fetchTableFilters')
    }).catch((error) => {
        context.errors = error.response.data.errors
    })
}

export const update = ({ dispatch, state, commit }, { filterId, payload, context }) => {
    return axios.patch(`/api/tableFilters/${filterId}`, payload).then((response) => {
        Bus.$emit('showAlert', {color: 'success', 'message': 'Änderungen erfolgreich gespeichert' });
        dispatch('fetchTableFilters')
    }).catch((error) => {
        context.errors = error.response.data.errors
    })
}

export const destroy = ({ dispatch, state, commit }, { filterId, payload, context }) => {
    return axios.delete(`/api/tableFilters/${filterId}`, payload).then((response) => {
        Bus.$emit('showAlert', {color: 'success', 'message': 'Filter erfolgreich gelöscht' });
        dispatch('fetchTableFilters')
    }).catch((error) => {
        context.errors = error.response.data.errors
    })
}

export const setActiveFilter = ({ commit }, data) => {
    commit('SET_ACTIVE_FILTER_DATA', data)
}

export const unsetActiveFilter = ({ state, commit }, tableId) => {
    if(state.activeFilters[tableId]) {
        commit('UNSET_ACTIVE_FILTER_DATA', tableId)
    }

}

export const setFilterDialog = ({ dispatch, state, commit }, value) => {
    commit('SET_FILTER_DIALOG', value)
    commit('SET_ACTIVE_FILTER_DATA', null)
}
