<template>
    <div>
        <v-card
            v-if="!authenticated"
            :loading="loading"
            max-width="344"
            class="mx-auto my-12"
        >
            <v-card-title>
                Login
            </v-card-title>
            <v-card-text>
                <v-form>
                    <v-text-field
                        v-model="form.email"
                        label="Email"
                    >

                    </v-text-field>
                    <v-text-field
                        v-model="form.password"
                        label="Password"
                        type="password"
                    >

                    </v-text-field>
                </v-form>

            </v-card-text>
            <v-card-actions>
                <v-btn
                    block
                    outlined
                    color="primary"
                    @click="submit"
                >
                    Login
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
    name: "Login",
    data() {
        return {
            loading: true,
            form: {
                email: '',
                password: ''
            }
        }
    },

    computed: {
        ...mapGetters({
            authenticated: 'auth/authenticated',
        }),

    },

    mounted() {
        this.loading = false
        $("label\[for=\'email\'\]").addClass('v-label--active');
        $("label\[for=\'password\'\]").addClass('v-label--active');
    },

    methods: {
        ...mapActions({
            fetchMe: 'auth/fetchMe'
        }),

        async submit () {
            const self = this
            axios.get('/sanctum/csrf-cookie').then(function (){
                axios.post('/api/login', self.form).then(function (){
                    self.fetchMe()
                })
            })

        },
    }
}
</script>

<style scoped>

</style>
