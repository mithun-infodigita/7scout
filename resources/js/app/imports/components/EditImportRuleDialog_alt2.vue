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
                                v-model="importRule.column"
                                label="Column"
                                :items="columns"
                                item-text="name"
                                item-value="name"
                                :error-messages="errors.columns"
                            >
                            </v-select>
                            <v-select
                                v-model="importRule.rule_type"
                                label="Rule type"
                                :items="ruleTypes"
                                :error-messages="errors.rule_type"
                            >
                            </v-select>

                            <v-text-field
                                label="Part value script"
                                v-model="importRule.part_value_script"
                                :error-messages="errors.part_value_script"
                            >

                            </v-text-field>

                            <v-select
                                v-model="importRule.compare_type"
                                label="Compare type"
                                :items="compareTypes"
                                :error-messages="errors.compare_type"
                            >
                            </v-select>

                            <v-text-field
                                label="Compare value"
                                v-model="importRule.compare_value"
                                :error-messages="errors.compare_value"
                            >

                            </v-text-field>
                        </v-col>

                        <v-col>
                            <v-select
                                v-if="groups"
                                v-model="importRule.group_id"
                                label="Group"
                                :items="groups"
                                item-text="name"
                                item-value="id"
                                :error-messages="errors.group_id"
                            >
                            </v-select>
                            <v-text-field
                                label="Map value script"
                                v-model="importRule.map_value_script"
                                :error-messages="errors.map_value_script"
                            >

                            </v-text-field>
                            <v-text-field
                                label="Text value"
                                v-model="importRule.text_value"
                                :error-messages="errors.text_value"
                            >

                            </v-text-field>
                            Category
                            <v-treeview
                                v-if="categories"
                                v-model="importRule.category_ids"
                                selectable
                                :items="categories"
                            ></v-treeview>

                            <template
                                v-if="errors.category_ids"
                            >
                                <span class="red--text">
                                          {{errors.category_ids}}
                                </span>
                            </template>
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
name: "CreateRuleDialog",
    components: {

    },
    data() {
        return {
            loading: true,
            columns: null,
            ruleTypes: ['category', 'group', 'map_text', 'fix_text', 'compare_text', 'full_record'],
            compareTypes: ['contains', 'equal'],
            categories: null,
            groups: null,
            errors: []
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


        axios.get(`/api/categories?type=tree`).then((response) =>{
            this.categories = response.data
        })

        axios.get(`/api/groups`).then((response) =>{
            this.groups = response.data
        })

        axios.get(`/api/columns`).then((response) =>{
            this.columns = response.data
        })
    }
}

</script>

<style lang="scss">

</style>
