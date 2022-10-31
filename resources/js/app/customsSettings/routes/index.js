import { CustomsSettings } from '../views'

export default [
    {
        path: '/customsSettings',
        component: CustomsSettings,
        name: 'customsSettings',
        meta: {
            guest: false,
            needsAuth: true
        }
    }

]
