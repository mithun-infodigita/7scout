import { Imports, Import } from '../views'

export default [
    {
        path: '/imports',
        component: Imports,
        name: 'imports',
        meta: {
            guest: false,
            needsAuth: true
        }
    },
    {
        path: '/imports/:importId/show',
        component: Import,
        name: 'imports.show',
        meta: {
            guest: false,
            needsAuth: true
        },
        props: true
    }
]
