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
                Create Token
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
                    v-if="showForm"
                    ref="form"
                >
                    <v-text-field
                        autofocus
                        label="Name"
                        v-model="form.name"
                        :error-messages="errors.name"
                    >

                    </v-text-field>

                </v-form>
                <template
                    v-else
                >
                    <h5>
                        New token
                    </h5>
                    <div
                        class="mt-2"
                    >
                        {{plainTextToken}}
                    </div>
                </template>
            </v-card-text>
            <v-card-actions>
                <v-btn
                    v-if="showForm"
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
name: "CreateTokenDialog",
    data() {
        return {
            loading: true,
            form: {
                name: '',
            },
            showForm: true,
            plainTextToken: null,
            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            user: 'users/user',
        }),

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
            setToken: 'users/setToken'
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/admin/users/${this.user.id}/tokens`, this.form).then((response) => {
                self.$refs.form.reset()
                self.showForm = false
                self.plainTextToken = response.data.plainTextToken
                self.loading = false
                Bus.$emit('token:created');
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Token successfully created!'});
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
