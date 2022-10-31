import '@mdi/font/css/materialdesignicons.css'
import Vue from 'vue'
import Vuetify from 'vuetify/lib'

Vue.use(Vuetify)

const opts = {}

import de from 'vuetify/es5/locale/de'
import en from 'vuetify/es5/locale/en'

Vue.use(Vuetify)


export default new Vuetify({
    icons: {
        iconfont: 'mdi',
    },
    lang: {
        locales: { de, en },
        current: 'en',
    }
})
