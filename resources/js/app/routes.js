import auth from './auth/routes'
import dashboard from './dashboard/routes'
import columns from './columns/routes'
import categories from './categories/routes'
import groups from './groups/routes'
import facets from './facets/routes'
import producers from './producers/routes'
import dispatchLocations from './disptachLocations/routes'
import imports from './imports/routes'
import partIndexes from './partIndexes/routes'
import customsSettings from './customsSettings/routes'
import users from './admin/users/routes'

export default [
    ...auth,
    ...dashboard,
    ...columns,
    ...categories,
    ...groups,
    ...facets,
    ...producers,
    ...dispatchLocations,
    ...imports,
    ...partIndexes,
    ...customsSettings,
    ...users

]
