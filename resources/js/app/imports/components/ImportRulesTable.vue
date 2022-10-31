<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>
        <EditImportRuleDialog v-bind:showDialog.sync="showEditImportRuleDialog" v-if="importRule"></EditImportRuleDialog>
    </div>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
import EditImportRuleDialog from "./EditImportRuleDialog";
export default {

name: "ImportRulesTable",
    components: {EditImportRuleDialog},
    data() {
        return {
            loading: true,
            showEditImportRuleDialog: false
        }
    },

    computed: {
        ...mapGetters({
            singleImport: 'imports/singleImport',
            importRule: 'imports/importRule'
        }),

    },
    methods: {
        ...mapActions({
            fetchImportRule: 'imports/fetchImportRule',
            updateImportRulesOrder: 'imports/updateImportRulesOrder'
        }),
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'importRulesTable',
            headermenu: {
                width: 250,
            },
            expand:true,
            select: "row",
            headerRowHeight: 40,
            drag: true,

            columns: [
                {
                    id: "name",
                    header: ['Name', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },
                {
                    id: "order_column",
                    header: ['Order', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "source_reference_column_name",
                    header: ['Source column', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "reference_compare_type",
                    header: ['Reference compare type', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "map_reference_script",
                    header: ['Map reference script', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "map_file_name",
                    header: ['Map file', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },


                {
                    id: "map_column_name",
                    header: ['Map column', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "map_value_script",
                    header: ['Map value script', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

            ],


            on: {
                onItemDblClick: function (row) {
                    self.fetchImportRule(row.row).then(function (){
                        self.showEditImportRuleDialog =true
                    })
                },

                onBeforeLoad: function () {
                    this.clearAll()
                },

                onAfterLoad: function () {
                    self.loading = false

                    if (self.importRule && this.exists(self.importRule.id)) {
                        this.select(self.importRule.id)
                        this.showItem(self.importRule.id)
                    }

                },

                onAfterDrop: function () {
                    let orderedIds = []
                    this.eachRow(function(row){
                        orderedIds.push(row)
                    });
                    self.updateImportRulesOrder({orderedIds: orderedIds}).then(function (){
                        Bus.$emit('showAlert', {color : 'success', 'message' : 'order successfully changed'});
                    })
                }
            },

            url: `/api/imports/${self.singleImport.id}/importRules`

        })

        Bus.$on('importRules:updated', function () {
            self.webixId.load(`/api/imports/${self.singleImport.id}/importRules`)
        });

        Bus.$on('importRules:created', function () {
            self.webixId.load(`/api/imports/${self.singleImport.id}/importRules`)
        });
    },


    watch: {
        singleImport: {
            handler: function (value) {
                this.webixId.load(`/api/imports/${this.singleImport.id}/importRules`)
            },
        }
    },

    destroyed:function(){
        webix.$$(this.webixId).destructor();
    }
}
</script>

<style scoped>

</style>
