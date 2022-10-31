<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>

        <EditGroupColumnDialog v-bind:showDialog.sync="showEditGroupColumnDialog" v-if="groupColumn"></EditGroupColumnDialog>
    </div>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
import EditGroupColumnDialog from "./EditGroupColumnDialog";

export default {

name: "GroupColumnsTable",
    components: {EditGroupColumnDialog},
    data() {
        return {
            loading: true,
            showEditGroupColumnDialog: false,
            state: null
        }
    },

    computed: {
        ...mapGetters({
            group: 'groups/group',
            groupColumn: 'groups/groupColumn'
        }),
    },

    methods: {
        ...mapActions({
            fetchGroupColumn: 'groups/fetchGroupColumn',
            updateGroupColumnsOrder: 'groups/updateGroupColumnsOrder'
        }),
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'groupColumnsTable',
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
                    id: "column_name",
                    header: ['Column ', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
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
                    id: "show_in_table_label",
                    header: ['Show in table', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.show_in_table_label
                    }
                },

                {
                    id: "show_in_table_detail_label",
                    header: ['Show in table detail', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.show_in_table_detail_label
                    }
                },

                {
                    id: "show_in_detail_page_label",
                    header: ['Show in page detail', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.show_in_detail_page_label
                    }
                },

                {
                    id: "left_side_filter_label",
                    header: ['Left side filter', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.left_side_filter_label
                    }
                },

                {
                    id: "detail_filter_label",
                    header: ['Detail filter', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.detail_filter_label
                    }
                },
            ],


            on: {
                onItemDblClick: function (row) {
                    self.fetchGroupColumn({ groupId: self.group.id, groupColumnId: row.row}).then(function (){
                        self.showEditGroupColumnDialog =true
                    })
                },

                onAfterLoad: function () {
                    if (self.group && this.exists(self.group.id)) {
                        this.select(self.group.id)
                        this.showItem(self.group.id)
                    }
                },

                onAfterDrop: function () {
                    let groupColumnIds = []
                    this.eachRow(function(row){
                        groupColumnIds.push(row)
                    });
                    self.updateGroupColumnsOrder({groupId: self.group.id, orderIds: groupColumnIds}).then(function (){
                        Bus.$emit('showAlert', {color : 'success', 'message' : 'Order successfully changed'});
                    })
                }
            },

            url: `/api/groups/${self.group.id}/groupColumns`

        })

        Bus.$on('groupColumn:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load(`/api/groups/${self.group.id}/groupColumns`)
        });

        Bus.$on('groupColumn:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load(`/api/groups/${self.group.id}/groupColumns`)
        });

        this.loading = false
    },

    watch: {
        group: {
            handler: function (value) {
                this.webixId.clearAll()
                this.webixId.load(`/api/groups/${this.group.id}/groupColumns`)
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
