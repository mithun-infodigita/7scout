import Vue from 'vue'
import Vuex from 'vuex'
import auth from '../app/auth/vuex'
import dashboard from '../app/dashboard/vuex'
import columns from '../app/columns/vuex'
import categories from '../app/categories/vuex'
import groups from '../app/groups/vuex'
import facets from '../app/facets/vuex'
import producers from '../app/producers/vuex'
import dispatchLocations from '../app/disptachLocations/vuex'
import imports from '../app/imports/vuex'
import partIndexes from '../app/partIndexes/vuex'
import customsSettings from '../app/customsSettings/vuex'
import users from '../app/admin/users/vuex'
import tableFilters from '../app/globalFeatures/tableFilters/vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        auth: auth,
        dashboard: dashboard,
        columns: columns,
        categories: categories,
        groups: groups,
        facets: facets,
        producers: producers,
        dispatchLocations: dispatchLocations,
        imports: imports,
        partIndexes:partIndexes,
        customsSettings: customsSettings,
        users:users,
        tableFilters:tableFilters,
    }
})
