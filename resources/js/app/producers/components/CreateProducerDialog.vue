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
                Create producer
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
                    <v-select
                        v-if="dispatchLocations"
                        v-model="form.dispatch_location_ids"
                        :items="dispatchLocations"
                        label="Dispatch locations"
                        item-text="name"
                        item-value="id"
                        multiple
                    >

                    </v-select>

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
import {mapActions, mapGetters} from 'vuex';

export default {
name: "CreateProducerDialog",
    data() {
        return {
            loading: true,
            form: {
                name: '',
                unique_id: '',
                dispatch_location_ids: []
            },
            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            dispatchLocations: 'dispatchLocations/dispatchLocations'
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
            fetchDispatchLocations: 'dispatchLocations/fetchDispatchLocations'
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/producers`, this.form).then((response) => {
                Bus.$emit('producer:created');
                self.$refs.form.reset()
                self.loading = false
                self.dialog = false
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Producer successfully created!'});
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },
    },

    mounted() {
        const self = this

        this.fetchDispatchLocations()

        this.loading = false
    }
}
</script>

<style scoped>

</style>
