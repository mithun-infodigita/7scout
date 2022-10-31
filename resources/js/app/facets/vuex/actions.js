export const fetchFacets = ({ commit }) => {
    return  axios.get(`/api/facets`).then((response) => {
        commit('SET_FACETS_DATA', response.data)
    })
}

export const fetchFacet = ({ commit }, id) => {
    return  axios.get(`/api/facets/${id}`).then((response) => {
        commit('SET_FACET_DATA', response.data)
    })
}

export const setFacet = ({ state, commit }, data) => {
    commit('SET_FACET_DATA', data)
}

export const updateFacetOrder = ({ dispatch, state, commit }, facetIds) => {
    return axios.patch(`/api/facets/updateOrder`, facetIds).then((response) => {
    }).catch((error) => {

    })
}
