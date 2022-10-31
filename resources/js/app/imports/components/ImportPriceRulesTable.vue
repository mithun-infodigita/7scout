<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>
        <EditImportPriceRuleDialog v-bind:showDialog.sync="showEditImportPriceRuleDialog" v-if="importPriceRule"></EditImportPriceRuleDialog>
    </div>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
import EditImportRuleDialog from "./EditImportRuleDialog";
import EditImportPriceRuleDialog from "./EditImportPriceRuleDialog";
export default {

name: "ImportPriceRulesTable",
    components: {EditImportPriceRuleDialog, EditImportRuleDialog},
    data() {
        return {
            loading: true,
            showEditImportPriceRuleDialog: false
        }
    },

    computed: {
        ...mapGetters({
            singleImport: 'imports/singleImport',
            importPriceRule: 'imports/importPriceRule'
        }),

    },
    methods: {
        ...mapActions({
            fetchImportPriceRule: 'imports/fetchImportPriceRule',
        }),
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'importPriceRulesTable',
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
                    id: "map_value_script",
                    header: ['Map value script', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },


                {
                    id: "currency",
                    header: ['Currency', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "country",
                    header: ['Country', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

            ],

            on: {
                onItemDblClick: function (row) {
                    self.fetchImportPriceRule(row.row).then(function (){
                        self.showEditImportPriceRuleDialog =true
                    })
                },

                onBeforeLoad: function () {
                    this.clearAll()
                },

                onAfterLoad: function () {
                    self.loading = false

                    if (self.importPriceRule && this.exists(self.importPriceRule.id)) {
                        this.select(self.importPriceRule.id)
                        this.showItem(self.importPriceRule.id)
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

            url: `/api/imports/${self.singleImport.id}/importPriceRules`

        })

        Bus.$on('importPriceRules:updated', function () {
            self.webixId.load(`/api/imports/${self.singleImport.id}/importPriceRules`)
        });

        Bus.$on('importPriceRules:created', function () {
            self.webixId.load(`/api/imports/${self.singleImport.id}/importPriceRules`)
        });

    },


    watch: {
        singleImport: {
            handler: function (value) {
                this.webixId.load(`/api/imports/${this.singleImport.id}/importPriceRules`)
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
