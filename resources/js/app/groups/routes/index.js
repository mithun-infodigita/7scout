import { Groups, Group } from '../views'

export default [
    {
        path: '/groups',
        component: Groups,
        name: 'groups',
        meta: {
            guest: false,
            needsAuth: true
        }
    },
    {
        path: '/groups/:groupId/show',
        component: Group,
        name: 'groups.show',
        meta: {
            guest: false,
            needsAuth: true
        },
        props: true
    }

]
