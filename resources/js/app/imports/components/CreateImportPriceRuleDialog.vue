<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="1200px"
        scrollable
    >
        <v-card
        :loading="loading"
        >
            <v-card-title>
                Create price rule
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
                    <v-row>
                        <v-col>

                            <v-text-field
                                label="Name"
                                v-model="form.name"
                                :error-messages="errors.name"
                            >
                            </v-text-field>

                            <v-select
                                v-if="columns"
                                v-model="form.source_reference_column_id"
                                :items="columns"
                                item-value="id"
                                item-text="name"
                                label="Reference column id"
                            >
                            </v-select>

                            <v-radio-group
                                v-model="form.reference_compare_type"
                                row
                            >
                                <v-radio
                                    v-for="compareType in  compareTypes"

                                    :label="compareType"
                                    :value="compareType"
                                ></v-radio>
                            </v-radio-group>


                            <v-select
                                v-if="priceFiles"
                                v-model="form.map_file_id"
                                :items="priceFiles"
                                item-value="id"
                                item-text="name"
                                label="Map file"
                            >
                            </v-select>

                            <v-text-field
                                label="Map reference script"
                                v-model="form.map_reference_script"
                                :error-messages="errors.map_reference_script"
                            >
                            </v-text-field>

                        </v-col>

                        <v-col>
                            <v-select
                                v-if="currencies"
                                v-model="form.currency"
                                :items="currencies"
                                label="Currency"
                            >
                            </v-select>

                            <v-select
                                v-if="countries"
                                v-model="form.country"
                                :items="countries"
                                label="Country"
                            >
                            </v-select>
                            <v-text-field
                                label="Map value script"
                                v-model="form.map_value_script"
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
name: "CreateImportPriceRuleDialog",
    components: {

    },
    data() {
        return {
            loading: true,
            form: {
                name: '',
                source_reference_column_id: '',
                reference_compare_type: 'contains',
                map_reference_script: '',
                map_column_id: null,
                map_file_id: null,
                map_value_script: '',
                country: null,
                currency: null

            },
            priceFiles: null,
            columns: null,
            compareTypes: ['contains', 'equal', 'included'],
            errors: [],
            countries: null,
            currencies: null
        }
    },

    props: {
        showDialog: [Boolean, String]
    },


    computed: {
        ...mapGetters({
            singleImport: 'imports/singleImport',
            importRule: 'imports/importRule',
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
            axios.post(`/api/imports/${this.singleImport.id}/importPriceRules`, this.form).then((response) => {
                self.setImportPriceRule(response.data).then(function (){
                    self.loading = false
                    self.$refs.form.reset()
                    Bus.$emit('importPriceRules:created');
                    self.setCopyImportRuleId(null)
                    self.dialog = false
                })
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
                self.setCopyImportRuleId(null)
            })
        },

    },

    mounted() {
        this.loading = false

        const self = this

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
    },

    watch: {


    }
}

</script>

<style lang="scss">

</style>
