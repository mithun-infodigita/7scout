<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="800px"
    >
        <v-card
        :loading="loading"
        >
            <v-card-title>
                Create group column
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
                                v-if="columns && groupColumns"
                                :items="columns"
                                label="Column"
                                v-model="form.column_id"
                                item-text="name"
                                item-value="id"
                                :item-disabled="disableTakenColumns"
                            >

                            </v-select>

                            <v-switch
                                v-model="form.show_in_table"
                                label="Show in table"
                            >

                            </v-switch>

                            <v-switch
                                v-model="form.show_in_table_detail"
                                label="Show in table detail"
                            >

                            </v-switch>

                            <v-switch
                                v-model="form.show_in_detail_page"
                                label="Show in detail page"
                            >

                            </v-switch>

                        </v-col>
                        <v-col>

                            <v-text-field
                                :key="language.key"
                                v-for="language in languages"
                                :label="`${language.value} table`"
                                :value="`${language.key}_table`"
                                v-model="form[`${language.key}_table`]"
                            >

                            </v-text-field>


                            <v-text-field
                                :key="language.key"
                                v-for="language in languages"
                                :label="`${language.value} detail`"
                                :value="`${language.key}_detail`"
                                v-model="form[`${language.key}_detail`]"
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
import {fetchGroupColumns} from "../vuex/actions";

export default {
name: "CreateGroupColumnDialog",
    data() {
        return {
            loading: true,
            form: {
                name: '',
                column_id: '',
                show_in_table: 0,
                detail_filter: 0,
                show_in_table_detail: 0,
                show_in_detail_page: 0,
                left_side_filter: 0
            },
            languages: null,
            columns: null,
            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            group: 'groups/group',
            groupColumns: 'groups/groupColumns'
        }),

        takenColumnIds: function () {
            return _.map(this.groupColumns, 'column_id')
        },

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
            fetchGroupColumns: 'groups/fetchGroupColumns',
            setGroupColumn: 'groups/setGroupColumn',
        }),

        disableTakenColumns (item) {
            return _.includes(this.takenColumnIds, item.id)
        },

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/groups/${self.group.id}/groupColumns`, this.form).then((response) => {
                self.$refs.form.reset()
                self.fetchGroupColumns(self.group.id)
                self.setGroupColumn(response.data).then(function (){
                    Bus.$emit('groupColumn:created');
                    self.loading = false
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Group column successfully created!'});
                self.dialog = false
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },
    },

    mounted() {
        const self = this

        axios.get(`/api/languages`).then((response) => {
            this.languages = response.data
        })

        axios.get(`/api/columns`).then((response) => {
            this.columns = response.data
        })

        this.loading = false
    },

    watch: {
        group: {
            handler: function (value) {
                this.fetchGroupColumns(this.group.id)
            },
            immediate: true
        }
    },

}
</script>

<style scoped>

</style>
