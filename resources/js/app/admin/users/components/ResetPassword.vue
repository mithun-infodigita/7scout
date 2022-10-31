<template>
    <v-card
        :loading="loading"
    >
        <v-card-text
            class="pl-0 pr-0"
        >
            Create new password and tell it to {{user.full_name}}.
            <v-form
                ref="form"
            >
                <v-text-field
                    label="Passwort"
                    v-model="form.password"
                    :error-messages="errors.password"
                    type="password"
                >
                </v-text-field>

            </v-form>
        </v-card-text>
        <v-card-actions
            class="pl-0 pr-0"
        >
            <v-btn
                text
                color="primary"
                :disabled="loading"
                @click="submit"
            >
                Update password
            </v-btn>

        </v-card-actions>
    </v-card>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Bus from "../../../../bus";

export default {
    name: "ResetPassword",

    data () {
        return  {
            loading: true,
            errors: [],
            form: {
                password: ''
            }
        }
    },
    computed: {
        ...mapGetters({
            user: 'users/user',
        }),
    },

    methods: {
        ...mapActions({
            setUser: 'users/setUser'
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.put(`/api/admin/users/${this.user.id}/updatePassword`, this.form).then((response) => {
                self.$refs.form.reset()
                self.setUser(response.data).then(function (){
                    Bus.$emit('user:updated');
                    self.loading = false
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Password successfully changed!'});
            }).catch((error) => {
                self.loading = false
                this.errors = error.response.data.errors
            })
        },
    },

    mounted() {
        this.loading = false
    },
}
</script>

<style scoped>

</style>
