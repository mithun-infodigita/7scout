<template>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
export default {

name: "ProducersTable",

    computed: {
        ...mapGetters({

        }),
    },

    mounted: function () {
        const self = this


        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'producersTable',
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
                    self.$router.push({name: 'producers.show', params: {producerId: row.row}})
                },

                onAfterLoad: function () {
                    if (self.producer && this.exists(self.producer.id)) {
                        this.select(self.group.id)
                        this.showItem(self.group.id)
                    }

                },
            },

            url: '/api/producers'

        })

        Bus.$on('producer:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/producers')
        });

        Bus.$on('producer:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/producers')
        });

    },

    destroyed:function(){
        webix.$$(this.webixId).destructor();
    }
}
</script>

<style scoped>

</style>
