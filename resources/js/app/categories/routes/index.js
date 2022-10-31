import { Categories } from '../views'

export default [
    {
        path: '/categories',
        component: Categories,
        name: 'categories',
        meta: {
            guest: false,
            needsAuth: true
        }
    },


]
