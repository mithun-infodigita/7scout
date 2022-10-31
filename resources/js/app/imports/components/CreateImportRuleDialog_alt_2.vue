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
                Create rule
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
                                v-model="form.column"
                                label="Column"
                                :items="columns"
                                item-text="name"
                                item-value="name"
                                :error-messages="errors.column"
                            >
                            </v-select>

                            <v-select
                                v-if="columns"
                                v-model="form.source_column"
                                label="Source column"
                                :items="columns"
                                item-text="name"
                                item-value="name"
                                :error-messages="errors.source_column"
                            >
                            </v-select>

                            <v-select
                                v-model="form.rule_type"
                                label="Rule type"
                                :items="ruleTypes"
                                :error-messages="errors.rule_type"
                            >
                            </v-select>

                            <v-text-field
                                label="Part value script"
                                v-model="form.part_value_script"
                                :error-messages="errors.part_value_script"
                            >

                            </v-text-field>

                            <v-select
                                v-model="form.compare_type"
                                label="Compare type"
                                :items="compareTypes"
                                :error-messages="errors.compare_type"
                            >
                            </v-select>

                            <v-text-field
                                label="Compare value"
                                v-model="form.compare_value"
                                :error-messages="errors.compare_value"
                            >

                            </v-text-field>
                        </v-col>

                        <v-col>


                            <v-select
                                v-if="groups"
                                v-model="form.group_id"
                                label="Group"
                                :items="groups"
                                item-text="name"
                                item-value="id"
                                :error-messages="errors.group_id"
                            >
                            </v-select>
                            <v-text-field
                                label="Map value script"
                                v-model="form.map_value_script"
                                :error-messages="errors.map_value_script"
                            >

                            </v-text-field>
                            <v-text-field
                                label="Text value"
                                v-model="form.text_value"
                                :error-messages="errors.text_value"
                            >

                            </v-text-field>
                            Category
                            <div id="category">

                            </div>
                            <v-treeview
                                v-if="categories"
                                v-model="category_ids"
                                selectable
                                :items="categories"
                            ></v-treeview>
                            <template
                                v-if="errors.category_id"
                            >
                                <span class="red--text">
                                   {{errors.category_id}}
                                </span>
                            </template>
                            <template
                                v-if="category_ids.length > 1"
                            >
                                <span class="red--text">
                                   Select only one category
                                </span>
                            </template>
                            {{form.category_id}}
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
            form: {
                name: '',
                column: '',
                source_column: '',
                script: '',
                rule_type: '',
                part_value_script: '',
                compare_value: '',
                group_id: '',
                map_value_script: '',
                text_value: '',
                category_id: ''
            },
            category_ids: [],
            columns: null,
            ruleTypes: ['category', 'group', 'map', 'fix_text', 'compare_text', 'full_record'],
            compareTypes: ['contains', 'equal'],
            categories: null,
            groups: null,
            errors: []
        }
    },

    props: {
        showDialog: [Boolean, String]
    },


    computed: {
        ...mapGetters({
            singleImport: 'imports/singleImport',
            importRule: 'imports/importRule',
            copyImportRuleId: 'imports/copyImportRuleId'
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
            axios.post(`/api/imports/${this.singleImport.id}/importRules`, this.form).then((response) => {
                self.setImportRule(response.data).then(function (){
                    self.loading = false
                    self.$refs.form.reset()
                    Bus.$emit('importRules:created');
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

        axios.get(`/api/categories?type=tree`).then((response) =>{
            this.categories = response.data
        })

        axios.get(`/api/groups`).then((response) =>{
            this.groups = response.data
        })

        axios.get(`/api/columns`).then((response) =>{
            this.columns = response.data
        })


    },

    watch: {
        copyImportRuleId: function (val) {
            if(val) {
                this.form = this.importRule
            }
        },

        category_ids: function (val) {
            if(val.length) {
                if(val.length === 1){
                    this.form.category_id = val[0]
                }
                else {
                    this.form.category_id = null
                }
            }
            else {
                this.form.category_id = null
            }
        }
    }
}

</script>

<style lang="scss">

</style>
