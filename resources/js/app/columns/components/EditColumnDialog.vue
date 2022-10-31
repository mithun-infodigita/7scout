<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="1000px"
    >
        <v-card
        :loading="loading"
        >
            <v-card-title>
                Edit column
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="red"
                    @click="deleteColumn"
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
                                v-model="column.name"
                                :error-messages="errors.name"
                            >
                            </v-text-field>

                            <v-text-field
                                label="Default name german"
                                v-model="column.default_de_name"
                                :error-messages="errors.default_de_name"
                            >
                            </v-text-field>

                            <v-text-field
                                label="Default name french"
                                v-model="column.default_fr_name"
                                :error-messages="errors.default_fr_name"
                            >
                            </v-text-field>

                        </v-col>
                        <v-col>
                            <v-select
                                v-model="column.type"
                                label="Type"
                                :items="types"
                                :error-messages="errors.type"
                            >

                            </v-select>

                            <v-text-field
                                label="Default name english"
                                v-model="column.default_en_name"
                                :error-messages="errors.default_en_name"
                            >
                            </v-text-field>

                            <v-text-field
                                label="Default name italian"
                                v-model="column.default_it_name"
                                :error-messages="errors.default_it_name"
                            >
                            </v-text-field>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col>
                            <v-switch
                                color="primary"
                                label="Nullable"
                                v-model="column.nullable"
                            >

                            </v-switch>

                            <v-switch
                                color="primary"
                                label="Import parts table"
                                v-model="column.import_parts_table"
                            >

                            </v-switch>

                            <v-switch
                                color="primary"
                                label="Index table"
                                v-model="column.index_table"
                            >

                            </v-switch>

                            <v-switch
                                color="primary"
                                label="Unique"
                                v-model="column.unique"
                            >

                            </v-switch>
                        </v-col>
                        <v-col>
                            <v-switch
                                color="primary"
                                label="Show in frontend"
                                v-model="column.default_show_in_frontend"
                            >

                            </v-switch>
                            <v-switch
                                color="primary"
                                label="Show in Table"
                                v-model="column.default_show_in_table"
                            >

                            </v-switch>
                            <v-switch
                                color="primary"
                                label="Show in table detail"
                                v-model="column.default_show_in_table_detail"
                            >

                            </v-switch>
                            <v-switch
                                color="primary"
                                label="Show in page detail"
                                v-model="column.default_show_in_detail_page"
                            >

                            </v-switch>
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
name: "EditColumnDialog",
    data() {
        return {
            loading: true,
            languages: null,
            errors: [],
            types: [
                'text',
                'string',
                'boolean',
                'longtext'
            ]
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            column: 'columns/column',
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
            setColumn: 'columns/setColumn',
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.patch(`/api/columns/${this.column.id}`, this.column).then((response) => {
                self.setColumn(response.data).then(function (){
                    Bus.$emit('column:created');
                    self.loading = false
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Column successfully created!'});
                self.dialog = false
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

        deleteColumn() {
            const self = this
            this.loading = true
            if(confirm('Do you really want to delete this column?')) {
                axios.delete(`/api/columns/${self.column.id}`).then((response) => {
                    Bus.$emit('column:updated');
                    self.loading = false
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'Column successfully deleted!'});
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
        axios.get(`/api/languages`).then((response) => {
            this.languages = response.data.languages
        })
        this.loading = false
    }
}
</script>

<style scoped>

</style>
