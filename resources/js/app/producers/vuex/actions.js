export const fetchProducers = ({ commit }) => {
    return  axios.get(`/api/producers`).then((response) => {
        commit('SET_PRODUCERS_DATA', response.data)
    })
}

export const fetchProducer = ({ commit }, id) => {
    return  axios.get(`/api/producers/${id}`).then((response) => {
        commit('SET_PRODUCER_DATA', response.data)
    })
}

export const setSelectedTab = ({ dispatch, state, commit}, value) => {
    commit('SET_SELECTED_TAB', value)
}
