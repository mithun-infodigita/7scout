<template>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
export default {

name: "DispatchLocationsTable",

    computed: {
        ...mapGetters({

        }),
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
            id: 'dispatchLocationsTable',
            headermenu: {
                width: 250,
            },
            expand:true,
            select: "row",
            headerRowHeight: 40,

            columns: [
                {
                    id: "id",
                    header: ['id', {content: "textFilter"}],
                    adjust: true, sort: "int"
                },

                {
                    id: "unique_id",
                    header: ['unique id', {content: "textFilter"}],
                    adjust: true,
                    sort: "int"
                },

                {
                    id: "name",
                    header: ['name', {content: "textFilter"}],
                    fillspace: true,
                    minWidth: 200,
                    sort: "string"
                },

                {
                    id: "active_label",
                    header: ['active', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.active_label
                    }
                },
                {
                    id: "created_at",
                    map: "(date)#created_at#",
                    header: ['created', {content: "dateRangeFilter"}],
                    sort: "date",
                    adjust: true,
                    format: webix.Date.dateToStr("%d.%m.%Y")
                }
            ],

            on: {
                onItemDblClick: function (row) {
                    self.$router.push({name: 'dispatchLocations.show', params: {dispatchLocationId: row.row}})
                },

                onAfterLoad: function () {
                    if (self.dispatchLocation && this.exists(self.dispatchLocation.id)) {
                        this.select(self.group.id)
                        this.showItem(self.group.id)
                    }

                },
            },

            url: self.endpoint

        })

        Bus.$on('dispatchLocation:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load(self.endpoint)
        });

        Bus.$on('dispatchLocation:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load(self.endpoint)
        });


        Bus.$on('producer:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load(self.endpoint)
        });
    },

    destroyed:function(){
        webix.$$(this.webixId).destructor();
    }
}
</script>

<style scoped>

</style>
