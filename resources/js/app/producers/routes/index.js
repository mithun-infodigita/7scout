import { Producers } from '../views'
import {Producer} from "../views";

export default [
    {
        path: '/producers',
        component: Producers,
        name: 'producers',
        meta: {
            guest: false,
            needsAuth: true
        }
    },

    {
        path: '/producers/:producerId/show',
        component: Producer,
        name: 'producers.show',
        meta: {
            guest: false,
            needsAuth: true
        },
        props: true
    }
]
