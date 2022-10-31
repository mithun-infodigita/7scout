import { Dashboard } from '../views'

export default [
    {
        path: '/dashboard',
        component: Dashboard,
        name: 'dashboard',
        meta: {
            guest: false,
            needsAuth: true
        }
    },


]
