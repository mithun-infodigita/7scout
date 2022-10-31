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
                Edit dispatchLocation
                <v-spacer></v-spacer>
<!--                <v-btn-->
<!--                    v-if="group.num_import_rules === 0 "-->
<!--                    text-->
<!--                    color="red"-->
<!--                    @click="deleteDispatchLocation"-->
<!--                >-->
<!--                    <v-icon>-->
<!--                        mdi-trash-can-outline-->
<!--                    </v-icon>-->
<!--                </v-btn>-->
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
                        v-model="dispatchLocation.name"
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
import {mapActions, mapGetters} from 'vuex';

export default {
name: "EditDispatchLocationDialog",
    data() {
        return {
            loading: true,
            languages: null,
            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            dispatchLocation: 'dispatchLocations/dispatchLocation',
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

        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.patch(`/api/dispatchLocations/${this.dispatchLocation.id}`, this.dispatchLocation).then((response) => {
                Bus.$emit('dispatchLocation:updated');
                self.loading = false
                Bus.$emit('showAlert', {color : 'success', 'message' : 'DispatchLocation successfully updated!'});
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

        deleteDispatchLocation() {
            const self = this
            this.loading = true
            if(confirm('Do you really want to delete this dispatchLocation?')) {
                axios.delete(`/api/dispatchLocation/${self.dispatchLocation.id}`).then((response) => {
                    Bus.$emit('dispatchLocation:deleted', self.dispatchLocation.id);
                    self.loading = false
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'DispatchLocation successfully deleted!'});
                    self.$router.push({name: 'dispatchLocations'})
                    self.dialog = false
                }).catch((error) => {
                    Bus.$emit('showAlert', {color : 'error', 'message' : error.response.data.message});
                    self.loading = false
                })
            }
            self.loading = false
        }
    },

    mounted() {
        const self = this

        this.loading = false
    }
}
</script>

<style scoped>

</style>
