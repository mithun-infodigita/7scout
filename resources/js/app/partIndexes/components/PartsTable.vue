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

name: "PartsTable",
    props: {
        parts: [Array]
    },

    data() {
        return {
            loading: true,
        }
    },


    mounted: function () {
        const self = this


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

            columns: [

                {
                    id: "id",
                    header: ['Id', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "string"
                },

                {
                    id: "producer_id",
                    header: ['Producer id', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "producer_name",
                    header: ['Producer', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 50,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "part_id",
                    header: ['Part id', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 50,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "part_number",
                    header: ['Part number', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "part_name",
                    header: ['Part name', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: false,
                    sort: "string",
                },

                {
                    id: "part_description",
                    header: ['Part description', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "cat_level_1",
                    header: ['Cat level 1', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "group",
                    header: ['Group', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },
            ],



            on: {
                onBeforeLoad: function () {
                    self.loading = true

                },

                onAfterLoad: function () {
                    self.loading = false

                },
            },

            data: self.parts

        })

    },

    destroyed:function(){
        webix.$$(this.webixId).destructor();
    }
}
</script>

<style scoped>

</style>
