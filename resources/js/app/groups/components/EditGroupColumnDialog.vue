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
                Edit group column
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="red"
                    @click="deleteGroupColumn"
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
                                v-model="groupColumn.name"
                                :error-messages="errors.name"
                            >

                            </v-text-field>

                            <v-select
                                v-if="columns && groupColumns"
                                :items="columns"
                                label="Column"
                                v-model="groupColumn.column_id"
                                item-text="name"
                                item-value="id"
                                :item-disabled="disableTakenColumns"
                            >

                            </v-select>

                            <v-switch
                                v-model="groupColumn.show_in_table"
                                label="Show in table"
                            >

                            </v-switch>

                            <v-switch
                                v-model="groupColumn.show_in_table_detail"
                                label="Show in table detail"
                            >

                            </v-switch>

                            <v-switch
                                v-model="groupColumn.show_in_detail_page"
                                label="Show in detail page"
                            >

                            </v-switch>

                        </v-col>
                        <v-col>

                            <v-text-field
                                v-if="groupColumn && languages"
                                :key="language.key"
                                v-for="language in languages"
                                :label="`${language.value} table`"
                                :value="`${language.key}_table`"
                                v-model="groupColumn[`${language.key}_table`]"
                            >

                            </v-text-field>


                            <v-text-field
                                v-if="groupColumn && languages"
                                :key="language.key"
                                v-for="language in languages"
                                :label="`${language.value} detail`"
                                :value="`${language.key}_detail`"
                                v-model="groupColumn[`${language.key}_detail`]"
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
name: "EditGroupColumnDialog",
    data() {
        return {
            loading: true,
            languages: null,
            columns: null,
            columnId: null,
            groupColumns: null,
            takenColumnIds: null,
            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            group: 'groups/group',
            groupColumn: 'groups/groupColumn'
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
            setGroupColumn: 'groups/setGroupColumn',
            fetchGroupColumns: 'groups/fetchGroupColumns',
        }),


        disableTakenColumns (item) {
            if(this.columnId === item.id) return false

            return _.includes(this.takenColumnIds, item.id)
        },

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.patch(`/api/groups/${this.group.id}/groupColumns/${this.groupColumn.id}`, this.groupColumn).then((response) => {
                this.fetchGroupColumns(self.group.id)
                self.setGroupColumn(response.data).then(function (){
                    Bus.$emit('groupColumn:updated');
                    self.loading = false
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Group column successfully updated!'});
                self.dialog = false
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

        deleteGroupColumn() {
            const self = this
            this.loading = true
            if(confirm('Do you really want to delete this group column?')) {
                axios.delete(`/api/groups/${self.group.id}/groupColumns/${this.groupColumn.id}`).then((response) => {
                    self.fetchGroupColumns(self.group.id)
                    Bus.$emit('groupColumn:updated');
                    self.setGroupColumn(null)
                    self.loading = false
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'Group column successfully deleted!'});
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
            this.languages = response.data
        })

        axios.get(`/api/columns`).then((response) => {
            this.columns = response.data
        })

        axios.get(`/api/groups/${self.group.id}/groupColumns`).then((response) => {
            this.groupColumns = response.data
            this.takenColumnIds = _.map(response.data, 'column_id')
        })

        this.columnId = this.groupColumn.column_id
        this.loading = false
    }
}
</script>

<style scoped>

</style>
