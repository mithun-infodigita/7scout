<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>
    </div>

</template>

<script>

import Bus from "../../../bus";
import {mapGetters} from "vuex";
export default {

name: "ImportsTable",
    data() {
        return {
            loading: true,
            showEditRuleDialog: false,
        }
    },

    props: {
        endpoint: String
    },

    mounted: function () {
        const self = this


        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'importsTable',
            headermenu: {
                width: 250,
            },
            expand:true,
            select: "row",
            headerRowHeight: 40,

            columns: [
                {
                    id: "id",
                    header: ['Id', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "status",
                    header: ['Status', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.status_label
                    }
                },

                {
                    id: "name",
                    header: ['Name', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "producer_name",
                    header: ['Producer', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "notification",
                    header: ['Notification', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "updated_at",
                    map: "(date)#updated_at#",
                    header: ['Updated', {content: "dateRangeFilter"}],
                    sort: "date",
                    adjust: false,
                    template: function (obj) {
                        return webix.i18n.parseFormatStr(obj.updated_at)
                    }
                },

                {
                    id: "created_at",
                    map: "(date)#created_at#",
                    header: ['Created', {content: "dateRangeFilter"}],
                    sort: "date",
                    adjust: false,
                    template: function (obj) {
                        return webix.i18n.parseFormatStr(obj.created_at)
                    }
                }
            ],


            on: {
                onItemDblClick: function (row) {
                    self.$router.push({name: 'imports.show', params: {importId: row.row}})
                },

                onAfterLoad: function () {
                    self.loading = false

                },
            },

            url: self.endpoint

        })
        Bus.$on('import:created', function () {
            self.webixId.load(self.endpoint)
        });

        Bus.$on('import:updated', function () {
            if(self.webixId) {
                self.webixId.load(self.endpoint)
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
