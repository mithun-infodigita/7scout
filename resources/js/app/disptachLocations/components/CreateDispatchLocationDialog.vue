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
                Create dispatchLocation
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
                        label="Name"
                        v-model="form.name"
                        :error-messages="errors.name"
                    >

                    </v-text-field>

                    <v-text-field
                        label="Unique id"
                        v-model="form.unique_id"
                        :error-messages="errors.name"
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
import Bus from "../../../bus";
import {mapActions} from 'vuex';

export default {
name: "CreateDispatchLocationDialog",
    data() {
        return {
            loading: true,
            form: {
                name: '',
                unique_id: '',
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

        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/dispatchLocations`, this.form).then((response) => {
                Bus.$emit('dispatchLocation:created');
                self.loading = false
                self.dialog = false
                Bus.$emit('showAlert', {color : 'success', 'message' : 'DispatchLocation successfully created!'});
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
