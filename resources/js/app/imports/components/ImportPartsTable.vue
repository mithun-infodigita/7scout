<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>

        <ImportPartDialog v-bind:showDialog.sync="showImportPartDialog" v-if="importPart"></ImportPartDialog>
    </div>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
import ImportPartDialog from "./ImportPartDialog";
export default {

name: "ImportPartsTable",
    components: {ImportPartDialog},
    props: {
        importId: [String, Number]
    },

    data() {
        return {
            datatableExists: false,
            loading: false,
            showImportPartDialog: false,
            tableId: 'importDataTable'
        }
    },

    computed: {
        ...mapGetters({
            importPart: 'imports/importPartData',
            activeFilters: 'tableFilters/activeFilters'
        }),
    },

    methods: {
        ...mapActions({
            fetchImportPart: 'imports/fetchImportPart',
        }),
    },

    mounted: function () {
        const self = this

        this.datatableExists = true

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'importDataTable',
            headermenu: {
                width: 250,
            },
            dragColumn: true,
            headerRowHeight: 40,
            autoConfig:true,
            resizeColumn:true,
            on: {

                onItemDblClick: function (row) {
                    self.fetchImportPart({importId: self.importId, partId: row.row}).then(function (){
                        self.showImportPartDialog = true
                    })
                },

                onBeforeLoad: function () {
                    self.loading = false

                },

                onAfterLoad: function () {
                    self.loading = false
                    // var state = webix.storage.local.get("importDataTable");
                    // if (state)
                    //     this.setState(state);

                    if(!self.defaultState) {
                        self.defaultState = this.getState()
                    }

                    if(self.tableId in self.activeFilters ) {
                        this.setState(JSON.parse(self.activeFilters[self.tableId].filter_data))
                    }

                },
                //
                //
                // onAfterColumnDrop : function(sourceId, targetId, event){
                //     if(!self.loading){
                //         webix.storage.local.put("importDataTable", this.getState())
                //     }
                // },
                //
                // onColumnResize: function(id,newWidth,oldWidth,user_action){
                //     if(!self.loading){
                //         webix.storage.local.put("importDataTable", this.getState())
                //     }
                // },
            },

            url: `/api/imports/${self.importId}/importPartsData`

        })


        Bus.$on('import:completed', function () {
            self.webixId.clearAll()
            self.webixId.load(`/api/imports/${self.importId}/importPartsData`)
        });

        Bus.$on('import:tableTruncated', function () {
            self.webixId.clearAll()
        });

        Bus.$on('filterTable:' + self.tableId, function (data) {
            self.filter = data
            if(self.datatableExists) {
                if(!self.defaultState) {
                    self.defaultState = $$(self.webixId).getState()
                }
                $$(self.webixId).setState(JSON.parse(data))
                //$$(self.webixId).getColumnConfig("part_name").header.push({content: "textFilter"});
            }

        });

        Bus.$on('resetFilterTable:' + self.tableId, function () {
            if(self.datatableExists) {
                $$(self.webixId).setState(self.defaultState)
               // $$(self.webixId).getColumnConfig("part_name").header.push({content: "textFilter"});
            }

        });
    },

    destroyed:function(){
        webix.$$(this.webixId).destructor();
    }
}
</script>

<style scoped>

</style>
