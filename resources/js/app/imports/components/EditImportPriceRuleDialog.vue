<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="1200px"
    >
        <v-card
        :loading="loading"
        >
            <v-card-title>
                Edit price rule
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="red"
                    @click="deleteImportPriceRule"
                >
                    <v-icon>
                        mdi-trash-can-outline
                    </v-icon>
                </v-btn>
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
                    <v-row>
                        <v-col>

                            <v-text-field
                                label="Name"
                                v-model="importPriceRule.name"
                                :error-messages="errors.name"
                            >
                            </v-text-field>

                            <v-select
                                v-if="columns"
                                v-model="importPriceRule.source_reference_column_id"
                                :items="columns"
                                item-value="id"
                                item-text="name"
                                label="Reference column id"
                            >
                            </v-select>

                            <v-radio-group
                                v-model="importPriceRule.reference_compare_type"
                                row
                            >
                                <v-radio
                                    v-for="(item, index) in  compareTypes"
                                    :key="index"
                                    :label="item"
                                    :value="item"
                                ></v-radio>
                            </v-radio-group>


                            <v-select
                                v-if="priceFiles"
                                v-model="importPriceRule.map_file_id"
                                :items="priceFiles"
                                item-value="id"
                                item-text="name"
                                label="Map price file"
                            >
                            </v-select>

                            <v-text-field
                                label="Map reference script"
                                v-model="importPriceRule.map_reference_script"
                                :error-messages="errors.map_reference_script"
                            >
                            </v-text-field>

                        </v-col>

                        <v-col>
                            <v-select
                                v-if="currencies"
                                v-model="importPriceRule.currency"
                                :items="currencies"
                                label="Currency"
                            >
                            </v-select>

                            <v-select
                                v-if="countries"
                                v-model="importPriceRule.country"
                                :items="countries"
                                label="Country"
                            >
                            </v-select>
                            <v-text-field
                                label="Map value script"
                                v-model="importPriceRule.map_value_script"
                                :error-messages="errors.map_value_script"
                            >
                            </v-text-field>

                        </v-col>

                    </v-row>
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
name: "EditImportPriceRuleDialog",
    components: {

    },
    data() {
        return {
            loading: true,
            priceFiles: null,
            columns: null,
            compareTypes: ['contains', 'equal', 'included'],
            errors: [],
            countries: null,
            currencies: null
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            singleImport: 'imports/singleImport',
            importPriceRule: 'imports/importPriceRule',

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
            setImportPriceRule: 'imports/setImportPriceRule',
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.patch(`/api/importPriceRules/${self.importPriceRule.id}`, this.importPriceRule).then((response) => {
                self.loading = false
                self.$refs.form.reset()
                Bus.$emit('importPriceRules:updated');
                self.dialog = false
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },


        deleteImportPriceRule() {
            const self = this
            this.loading = true
            if(confirm('Do you really want to delete this import rule?')) {
                axios.delete(`/api/importPriceRules/${self.importPriceRule.id}`).then((response) => {
                    Bus.$emit('importPriceRules:updated');
                    self.loading = false
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'Import rule successfully deleted!'});
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
        this.loading = false

        axios.get(`/api/imports/${this.singleImport.id}/priceFiles`).then((response) =>{
            this.priceFiles = response.data
        })

        axios.get(`/api/columns`).then((response) =>{
            this.columns = response.data
        })

        axios.get(`/api/currencies`).then((response) =>{
            this.currencies = response.data
        })

        axios.get(`/api/countries`).then((response) =>{
            this.countries = response.data
        })
    }
}

</script>

<style lang="scss">

</style>
