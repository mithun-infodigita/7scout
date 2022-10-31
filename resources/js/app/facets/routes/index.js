import { Facets } from '../views'

export default [
    {
        path: '/facets',
        component: Facets,
        name: 'facets',
        meta: {
            guest: false,
            needsAuth: true
        }
    }

]
