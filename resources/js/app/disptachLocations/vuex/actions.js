export const fetchDispatchLocations = ({ commit }) => {
    return  axios.get(`/api/dispatchLocations`).then((response) => {
        commit('SET_PRODUCERS_DATA', response.data)
    })
}

export const fetchDispatchLocation = ({ commit }, id) => {
    return  axios.get(`/api/dispatchLocations/${id}`).then((response) => {
        commit('SET_PRODUCER_DATA', response.data)
    })
}
