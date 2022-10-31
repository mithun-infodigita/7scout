<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>
        <EditFacetDialog v-bind:showDialog.sync="showEditFacetDialog" v-if="facet"></EditFacetDialog>
    </div>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
import EditFacetDialog from "./EditFacetDialog";
export default {

name: "FacetsTable",
    components: {EditFacetDialog},
    data() {
        return {
            loading: true,
            showEditFacetDialog: false,
            state: null
        }
    },

    computed: {
        ...mapGetters({
            facet: 'facets/facet',
        }),
    },

    methods: {
        ...mapActions({
            fetchFacet: 'facets/fetchFacet',
            updateFacetOrder: 'facets/updateFacetOrder'
        }),
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'facetsTable',
            headermenu: {
                width: 250,
            },
            expand:true,
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
                    header: ['name', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "de",
                    header: ['German', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "en",
                    header: ['English', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "fr",
                    header: ['French', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "it",
                    header: ['Italian', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "column_name",
                    header: ['Column', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "order_column",
                    header: ['Sort', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "item_sort",
                    header: ['Item sort', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "num_categories",
                    header: ['Num cat.', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },
            ],


            on: {
                onItemDblClick: function (row) {
                    self.fetchFacet(row.row).then(function (){
                        self.showEditFacetDialog =true
                    })
                },

                onAfterLoad: function () {
                    if (self.facet && this.exists(self.facet.id)) {
                        this.select(self.facet.id)
                        this.showItem(self.facet.id)
                    }
                },

                onAfterDrop: function () {
                    let facetIds = []
                    this.eachRow(function(row){
                        facetIds.push(row)
                    });
                    self.updateFacetOrder({facetIds: facetIds}).then(function (){
                        Bus.$emit('showAlert', {color : 'success', 'message' : 'Order successfully changed'});
                    })
                }
            },

            url: '/api/facets'

        })

        Bus.$on('facet:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/facets')
        });

        Bus.$on('facet:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/facets')
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
