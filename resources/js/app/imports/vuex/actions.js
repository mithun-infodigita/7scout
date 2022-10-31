export const fetchImport = ({ commit }, id) => {
    return  axios.get(`/api/imports/${id}`).then((response) => {
        commit('SET_IMPORT_DATA', response.data)
    })
}

export const setImport = ({ commit }, data) => {
    return  commit('SET_IMPORT_DATA', data)
}

export const setSelectedTab = ({ dispatch, state, commit}, value) => {
    commit('SET_SELECTED_TAB', value)
}

export const fetchImportRule = ({ commit }, id) => {
    return  axios.get(`/api/importRules/${id}/`).then((response) => {
        commit('SET_IMPORT_RULE_DATA', response.data)
    })
}

export const setImportRule = ({ commit }, data) => {
    commit('SET_IMPORT_RULE_DATA', data)
}

export const setCopyImportRuleId = ({ commit }, id) => {
    commit('SET_COPY_IMPORT_RULE_ID', id)
}


export const updateImportRulesOrder = ({ dispatch, state, commit }, orderIds) => {
    return axios.patch(`/api/importRules/updateOrder`, orderIds).then((response) => {
    }).catch((error) => {

    })
}


export const fetchImportPart = ({ commit }, {importId, partId}) => {
    return  axios.get(`/api/imports/${importId}/importPartsData/${partId}/`).then((response) => {
        commit('SET_IMPORT_PART_DATA', response.data)
    })
}

export const fetchImportPriceRule = ({ commit }, id) => {
    return  axios.get(`/api/importPriceRules/${id}/`).then((response) => {
        commit('SET_IMPORT_PRICE_RULE_DATA', response.data)
    })
}

export const setImportPriceRule = ({ commit }, data) => {
    commit('SET_IMPORT_PRICE_RULE_DATA', data)
}
