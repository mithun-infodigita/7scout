export const SET_TABLE_FILTERS_DATA = (state, data) => {
    state.tableFilters = data
}

export const SET_ACTIVE_FILTER_DATA = (state, data) => {
    state.activeFilters[data.table_id] = data
}

export const UNSET_ACTIVE_FILTER_DATA = (state, tableId) => {
    delete state.activeFilters.tableId;
}

export const SET_FILTER_DIALOG = (state, data) => {
    state.showIssueDialog = data
}


