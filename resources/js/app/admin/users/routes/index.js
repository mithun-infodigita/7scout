import { Users, User } from '../views'

export default [
    {
        path: '/admin/users',
        component: Users,
        name: 'admin.users',
        meta: {
            guest: false,
            needsAuth: true
        }
    },

    {
        path: '/admin/users/:userId',
        component: User,
        name: 'admin.users.show',
        props: true,
        meta: {
            guest: false,
            needsAuth: true
        }
    },

]
