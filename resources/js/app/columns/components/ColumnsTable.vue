<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>
        <EditColumnDialog v-bind:showDialog.sync="showEditColumnDialog" v-if="column"></EditColumnDialog>
    </div>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
import EditColumnDialog from "./EditColumnDialog";
export default {

name: "ColumnsTable",
    components: {EditColumnDialog},
    data() {
        return {
            loading: true,
            showEditColumnDialog: false,
            state: null
        }
    },

    computed: {
        ...mapGetters({
            column: 'columns/column'
        }),
    },

    methods: {
        ...mapActions({
            fetchColumn: 'columns/fetchColumn',
            updateColumnsOrder: 'columns/updateColumnsOrder'
        }),
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'columnsTable',
            headermenu: {
                width: 250,
            },
            select: "row",
            headerRowHeight: 40,
            drag: true,
            columns: [

                {
                    id: "id",
                    header: ['id', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",

                },

                {
                    id: "name",
                    header: ['Name', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "default_de_name",
                    header: ['Default name german', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "default_en_name",
                    header: ['Default name english', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "default_fr_name",
                    header: ['Default name french', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "default_it_name",
                    header: ['Default name italian', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "type",
                    header: ['Typ', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "nullable_label",
                    header: ['Nullable', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.nullable_label
                    }
                },

                {
                    id: "import_parts_table_label",
                    header: ['Import parts table', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.import_parts_table_label
                    }
                },

                {
                    id: "index_table_label",
                    header: ['Index table', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.index_table_label
                    }
                },

                {
                    id: "default_show_in_frontend_label",
                    header: ['Frontend', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.default_show_in_frontend_label
                    }
                },

                {
                    id: "default_show_in_table_label",
                    header: ['Table', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.default_show_in_table_label
                    }
                },

                {
                    id: "default_show_in_table_detail_label",
                    header: ['Table detail', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.default_show_in_table_detail_label
                    }
                },

                {
                    id: "default_show_in_detail_page_label",
                    header: ['Detail page', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.default_show_in_detail_page_label
                    }
                },
            ],

        on: {
            onItemDblClick: function (row) {
                self.fetchColumn(row.row).then(function (){
                    self.showEditColumnDialog =true
                })
            },

            onAfterLoad: function () {
                if (self.column && this.exists(self.column.id)) {
                    this.select(self.column.id)
                    this.showItem(self.column.id)
                }
            },

            onAfterDrop: function () {
                let columnIds = []
                this.eachRow(function(row){
                    columnIds.push(row)
                });
                self.updateColumnsOrder({columnIds: columnIds}).then(function (){
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'Order successfully changed'});
                    })
                }
            },

            url: '/api/columns'

        })

        Bus.$on('column:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/columns')
        });

        Bus.$on('column:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/columns')
        });

        this.loading = false
    },

    destroyed:function(){
        webix.$$(this.webixId).destructor();
    }
}
</script>

<style scoped>

</style>
