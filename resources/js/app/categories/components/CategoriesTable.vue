<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>

        <EditCategoryDialog v-bind:showDialog.sync="showEditCategoryDialog" v-if="category" :category-id="category.id" :key="category.id"></EditCategoryDialog>
    </div>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
import EditCategoryDialog from "./EditCategoryDialog";
export default {

name: "CategoriesTable",
    components: {EditCategoryDialog},
    data() {
        return {
            loading: true,
            showEditCategoryDialog: false,
            state: null
        }
    },

    computed: {
        ...mapGetters({
            category: 'categories/category',
        }),
    },

    methods: {
        ...mapActions({
            fetchCategory: 'categories/fetchCategory',
        }),
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "treetable",
            height: window.innerHeight - 200,
            id: 'categoriesTable',
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
                    minWidth: 150,
                    adjust: true,
                    sort: "int",
                    template:"{common.space()}{common.icon()} #id# "
                },

                {
                    id: "name",
                    header: ['Name', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string"
                },

                {
                    id: "level",
                    header: ['Level', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "int",
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
            ],

            on: {
                onItemDblClick: function (row) {
                    self.fetchCategory(row.row).then(function (){
                        self.showEditCategoryDialog =true
                    })
                },

                onAfterLoad: function () {
                    if (self.category && this.exists(self.category.id)) {
                        this.select(self.category.id)
                        this.showItem(self.category.id)
                    }
                    this.setState(self.state)
                },

                onAfterDrop: function (context) {
                    let orderedIds = []
                    this.eachRow(function(row){
                        orderedIds.push(row)
                    });

                    axios.patch(`/api/categories/${context.source}/updateOrder`, {parent_id: context.parent, order_ids: orderedIds}).then((response) => {
                        Bus.$emit('showAlert', {color : 'success', 'message' : 'Category successfully moved!'});
                    })
                }
            },



            url: '/api/categories?type=treeTable'

        })
        Bus.$on('category:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load(`/api/categories?type=treeTable`)
        });

        Bus.$on('category:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load(`/api/categories?type=treeTable`)

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
