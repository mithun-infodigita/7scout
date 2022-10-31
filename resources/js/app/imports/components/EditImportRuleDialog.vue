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
                Edit rule
                <v-spacer></v-spacer>
                <v-btn
                    outlined
                    color="primary"
                    @click="copyImportRule"
                    class="mr-2"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-content-copy
                    </v-icon>
                    Import Rule
                </v-btn>
                <v-btn
                    text
                    color="red"
                    @click="deleteImportRule"
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
                                v-model="importRule.name"
                                :error-messages="errors.name"
                            >
                            </v-text-field>

                            <v-select
                                v-if="columns"
                                v-model="importRule.source_reference_column_id"
                                :items="columns"
                                item-value="id"
                                item-text="name"
                                label="Reference column id"
                            >
                            </v-select>

                            <v-radio-group
                                v-model="importRule.reference_compare_type"
                                row
                            >
                                <v-radio
                                    v-for="compareType in  compareTypes"
                                    key="compareType"
                                    :label="compareType"
                                    :value="compareType"
                                ></v-radio>
                            </v-radio-group>


                            <v-select
                                v-if="additionalFiles"
                                v-model="importRule.map_file_id"
                                :items="additionalFiles"
                                item-value="id"
                                item-text="name"
                                label="Map file"
                            >
                            </v-select>

                            <v-text-field
                                label="Map reference script"
                                v-model="importRule.map_reference_script"
                                :error-messages="errors.map_reference_script"
                            >
                            </v-text-field>

                        </v-col>

                        <v-col>
                            <v-select
                                v-if="columns"
                                v-model="importRule.map_column_id"
                                :items="columns"
                                item-value="id"
                                item-text="name"
                                label="Map column id"
                            >
                            </v-select>

                            <v-text-field
                                label="Map value script"
                                v-model="importRule.map_value_script"
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
name: "EditImportRuleDialog",
    components: {

    },
    data() {
        return {
            loading: true,
            columns: null,
            compareTypes: ['contains', 'equal', 'included'],
            errors: [],
            basicFiles: null,
            additionalFiles: null,
        }
    },

    props: {
        showDialog: Boolean
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
            setImportRule: 'imports/setImportRule',
            setCopyImportRuleId: 'imports/setCopyImportRuleId'
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.patch(`/api/importRules/${self.importRule.id}`, this.importRule).then((response) => {
                self.loading = false
                self.$refs.form.reset()
                Bus.$emit('importRules:updated');
                self.dialog = false
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

        copyImportRule() {
            const self = this
            this.setCopyImportRuleId(this.importRule.id).then(function (){
                Bus.$emit('showCreateImportRuleDialog');
                self.dialog = false
            })
        },

        deleteImportRule() {
            const self = this
            this.loading = true
            if(confirm('Do you really want to delete this import rule?')) {
                axios.delete(`/api/importRules/${self.importRule.id}`).then((response) => {
                    Bus.$emit('importRules:updated');
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

        axios.get(`/api/imports/${this.singleImport.id}/basicFiles`).then((response) =>{
            this.basicFiles = response.data
        })

        axios.get(`/api/imports/${this.singleImport.id}/additionalFiles`).then((response) =>{
            this.additionalFiles = response.data
        })

        axios.get(`/api/columns`).then((response) =>{
            this.columns = response.data
        })
    }
}

</script>

<style lang="scss">

</style>
