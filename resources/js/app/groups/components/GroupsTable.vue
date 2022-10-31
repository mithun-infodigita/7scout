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
import {mapActions, mapGetters} from "vuex";
export default {

name: "GroupsTable",
    data() {
        return {
            loading: true,
            showEditGroupDialog: false,
            state: null
        }
    },

    computed: {
        ...mapGetters({
            group: 'groups/group',
        }),
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'groupsTable',
            headermenu: {
                width: 250,
            },
            expand:true,
            select: "row",
            headerRowHeight: 40,

            columns: [

                {
                    id: "id",
                    header: ['ID', {content: "textFilter"}],
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
                    id: "active_label",
                    header: ['Active', {content: "multiSelectFilter", suggest: {fitMaster: false, width: 200}}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                    template: function (obj) {
                        return obj.active_label
                    }
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
                    self.$router.push({name: 'groups.show', params: {groupId: row.row}})
                },

                onAfterLoad: function () {
                    if (self.group && this.exists(self.group.id)) {
                        this.select(self.group.id)
                        this.showItem(self.group.id)
                    }
                },
            },

            url: '/api/groups'

        })

        Bus.$on('group:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/groups')
        });

        Bus.$on('group:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/groups')
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
