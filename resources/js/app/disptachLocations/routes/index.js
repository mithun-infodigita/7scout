import { DispatchLocations } from '../views'
import {DispatchLocation} from "../views";

export default [
    {
        path: '/dispatchLocations',
        component: DispatchLocations,
        name: 'dispatchLocations',
        meta: {
            guest: false,
            needsAuth: true
        }
    },

    {
        path: '/dispatchLocations/:dispatchLocationId/show',
        component: DispatchLocation,
        name: 'dispatchLocations.show',
        meta: {
            guest: false,
            needsAuth: true
        },
        props: true
    }
]
