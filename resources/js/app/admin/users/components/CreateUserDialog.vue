<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="600px"
    >
        <v-card
        :loading="loading"
        >
            <v-card-title>
                Create User
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="primary"
                    @click="dialog = false"
                >
                    <v-icon>
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form
                    ref="form"
                >
                    <v-text-field
                        label="First name"
                        v-model="form.first_name"
                        :error-messages="errors.first_name"
                    >

                    </v-text-field>
                    <v-text-field
                        label="Last name"
                        v-model="form.last_name"
                        :error-messages="errors.last_name"
                    >
                    </v-text-field>

                    <v-text-field
                        label="Email"
                        v-model="form.email"
                        :error-messages="errors.email"
                    >
                    </v-text-field>

                    <v-text-field
                        label="Password"
                        v-model="form.password"
                        :error-messages="errors.password"
                        type="password"
                    >
                    </v-text-field>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-btn
                    text
                    color="primary"
                    :disabled="loading"
                    @click="submit"
                >
                    Store
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>

<script>
import Bus from "../../../../bus";
import {mapActions, mapGetters} from 'vuex';

export default {
name: "CreateUserDialog",
    data() {
        return {
            loading: true,
            form: {
                first_name: '',
                last_name: '',
                email: '',
                password: ''
            },

            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        dialog:{
            get(){
                return this.showDialog
            },
            set(val){
                return this.$emit("update:showDialog", val);
            }
        },
    },

    methods: {
        ...mapActions({
            setUser: 'users/setUser'
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/admin/users`, this.form).then((response) => {
                self.$refs.form.reset()
                self.setUser(response.data).then(function (){
                    Bus.$emit('user:created');
                    self.loading = false
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'User successfully created!'});
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },
    },

    mounted() {
        const self = this

        this.loading = false
    }
}
</script>

<style scoped>

</style>
