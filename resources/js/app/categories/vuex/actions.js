export const fetchCategories = ({ commit }, type) => {
    return  axios.get(`/api/categories?type=${type}`).then((response) => {
        commit('SET_CATEGORIES_DATA', response.data)
    })
}

export const fetchCategory = ({ commit }, id) => {
    return  axios.get(`/api/categories/${id}`).then((response) => {
        commit('SET_CATEGORY_DATA', response.data)
    })
}

export const setCategory = ({ state, commit }, data) => {
    commit('SET_CATEGORY_DATA', data)
}
