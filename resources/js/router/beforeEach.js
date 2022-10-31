import store from '../vuex'
import localforage from 'localforage'

const beforeEach = ((to, from, next) => {
    localforage.setItem('intended', to.name)
    store.dispatch('auth/checkAuthenticated').then(() => {
        if (to.meta.guest) {
            next({ name: 'login' })
            return
        }

        next()
    }).catch(() => {
        if (to.meta.needsAuth) {
            localforage.setItem('intended', to.name)
            next({ name: 'login' })
            return
        }

        next()
    })
})

export default beforeEach
