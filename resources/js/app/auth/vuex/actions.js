import router from "../../../router";
import localforage from 'localforage'

export const fetchMe = ({ commit }) => {
    const self = this
    return  axios.get('/api/me').then(function (response){
        commit('SET_AUTHENTICATED', true)
        commit('SET_USER_DATA', response.data)
        // localforage.getItem('intended', function (err, value) {
        //     console.log(router.from)
        //     if(!err) {
        //         //router.push(value)
        //     }
        //     else {
        //         router.push('dashboard')
        //     }
        // });
    }).catch(function (){
        commit('SET_AUTHENTICATED', false)
        commit('SET_USER_DATA', null)
        router.push('login')
    })
}

export const checkAuthenticated = ({ state }) => {
    if (!state.authenticated) {
        return Promise.reject('NO_STORAGE_TOKEN');
    }
    return Promise.resolve()
}

export const logout = ({ dispatch }, id) => {
    return axios.post('/api/logout').then((response) => {
        return dispatch('fetchMe')
    })
}






