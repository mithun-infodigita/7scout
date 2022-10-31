<template>
    <div>
        <v-card
            v-if="singleImport"
            :loading="loading"
        >
            <v-card-title>
                Import {{importId}} - {{singleImport.name}}
                <v-spacer></v-spacer>
                <span v-html="singleImport.status_label"></span>
                <v-spacer></v-spacer>
                <TableFilterDropDown table-id="importDataTable" key="importDataTable" v-if="selectedTab === 'importParts'"></TableFilterDropDown>
                <v-btn
                    class="ml-2"
                    color="primary"
                    outlined
                    small
                    @click="mergeImport"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-set-merge
                    </v-icon>
                    Merge
                </v-btn>

                <v-btn
                    class="ml-2"
                    color="primary"
                    outlined
                    small
                    @click="duplicateImport"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-content-copy
                    </v-icon>
                    Import
                </v-btn>

                <v-btn
                    class="ml-2"
                    color="primary"
                    outlined
                    small
                    @click="showEditImportDialog= true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-pencil-outline
                    </v-icon>
                    Import
                </v-btn>

                <v-btn
                    class="ml-2"
                    v-if="selectedTab === 'rules'"
                    color="primary"
                    outlined
                    small
                    @click="showCreateImportRuleDialog= true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Rule
                </v-btn>

                <v-btn
                    class="ml-2"
                    v-if="selectedTab === 'priceRules'"
                    color="primary"
                    outlined
                    small
                    @click="showCreateImportPriceRuleDialog= true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Price rule
                </v-btn>

                <v-btn
                    v-if="singleImport.type === 'basic import'"
                    class="ml-2"
                    color="primary"
                    outlined
                    small
                    @click="truncateTable"
                >
                    Truncate table
                </v-btn>
                <v-btn
                    class="ml-2"
                    color="primary"
                    outlined
                    small
                    @click="startImport"
                >
                    Start Import
                </v-btn>
                <v-menu offset-y>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            class="ml-2"
                            color="primary"
                            outlined
                            small
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon
                                color="primary"
                            >
                                mdi-dots-vertical
                            </v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item
                            @click="deleteImport"
                        >
                            <v-list-item-title>Delete Import</v-list-item-title>
                        </v-list-item>
                        <v-list-item
                            @click="deletePartsFromIndex"
                        >
                            <v-list-item-title>Delete all Parts from Index</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-card-title>
            <v-card-text>
                <ImportTabs :import-id="importId"></ImportTabs>
            </v-card-text>
        </v-card>
        <CreateImportRuleDialog v-bind:showDialog.sync="showCreateImportRuleDialog" v-if="singleImport"></CreateImportRuleDialog>
        <CreateImportPriceRuleDialog v-bind:showDialog.sync="showCreateImportPriceRuleDialog" v-if="singleImport"></CreateImportPriceRuleDialog>
        <EditImportDialog  v-bind:showDialog.sync="showEditImportDialog" v-if="singleImport"></EditImportDialog>
    </div>
</template>

<script>
import ImportTabs from "../components/ImportTabs";
import {mapActions, mapGetters} from "vuex";
import CreateImportRuleDialog from "../components/CreateImportRuleDialog";
import Bus from "../../../bus";
import EditImportDialog from "../components/EditImportDialog";
import CreateImportPriceRuleDialog from "../components/CreateImportPriceRuleDialog";
import TableFilterDropDown from "../../globalFeatures/tableFilters/components/TableFilterDropDown";
export default {
name: "Import",
    components: {TableFilterDropDown, CreateImportPriceRuleDialog, EditImportDialog, CreateImportRuleDialog, ImportTabs},
    props: {
        importId: [Number, String]
    },

    data() {
        return {
            loading: true,
            showCreateImportRuleDialog: false,
            showCreateImportPriceRuleDialog: false,
            showEditImportDialog: false,
        }
    },

    computed: {
        ...mapGetters({
            singleImport: 'imports/singleImport',
            selectedTab: 'imports/selectedTab',
        })
    },
    methods: {
        ...mapActions({
            fetchImport: 'imports/fetchImport',
            setImport: 'imports/setImport'
        }),
        startImport(){
            const self = this
            this.loading = true
            axios.get(`/api/imports/${this.importId}/partsImport`).then((response) => {
                Bus.$emit('showAlert', {color : 'success', 'message' : response.data});
                Bus.$emit('import:completed');
                self.fetchImport(self.singleImport.id)
                this.loading = false
            }).catch((error) => {
                Bus.$emit('showAlert', {color: 'error', 'message' : error.response.data.message});
                this.loading = false
            })
        },

        truncateTable() {
            const self = this
            this.loading = true
            if(confirm('Do you really want to truncate this table?')) {
                axios.delete(`/api/imports/${this.importId}/importPartsData/truncate`).then((response) => {
                    Bus.$emit('showAlert', {color: 'success', 'message': 'Table successfully truncated!'});
                    Bus.$emit('import:tableTruncated');
                    this.loading = false
                })
            }
        },

        duplicateImport() {
            const self = this
            this.loading = true
            axios.post(`/api/imports/${this.importId}/duplicateImport`).then((response) => {
                self.fetchImport(response.data.id).then(function (){
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'Import successfully duplicated!'});
                    self.$router.push({name: 'imports.show', params: {importId: response.data.id}})
                })
                this.loading = false
            })
        },

        deleteImport() {
            const self = this
            this.loading = true
            if (confirm("Do you really want to delete this import?")) {
                axios.delete(`/api/imports/${this.importId}`).then((response) => {
                    Bus.$emit('showAlert', {color: 'success', 'message': 'Import successfully deleted!'});
                    self.setImport(null).then(function () {
                        self.$router.push({name: 'imports'})
                        self.loading = false
                    })
                })
            }
        },

        deletePartsFromIndex() {
            const self = this
            if (confirm("Do you really want to delete all parts from index?")) {
                this.loading = true
                axios.delete(`/api/partIndexes/deletePartsFromIndex/${this.importId}`).then((response) => {
                    Bus.$emit('showAlert', {color: 'success', 'message': 'Parts successfully deleted!'});
                    self.loading = false
                })
            }
        },

        mergeImport(){
            const self = this
            if(confirm("Do you really want to merge this import?")) {
                this.loading = true
                axios.get(`/api/imports/${this.importId}/mergeImport`).then((response) => {
                    Bus.$emit('showAlert', {color: 'success', 'message': 'Import successfully merged'});
                    Bus.$emit('import:merged');
                    self.fetchImport(self.singleImport.id)
                    this.loading = false
                }).catch((error) => {
                    Bus.$emit('showAlert', {color: 'error', 'message': error.response.data.message});
                    this.loading = false
                })
            }
        },
    },
    mounted() {
        const self = this

        this.loading = false

        Bus.$on('showCreateImportRuleDialog', function () {
            self.showCreateImportDialog = true
        });

        this.fetchImport(this.importId)

    }

}
</script>

<style scoped>

</style>
