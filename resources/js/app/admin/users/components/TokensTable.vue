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

import Bus from "../../../../bus";
import {mapGetters} from "vuex";

export default {

name: "TokensTable",
    components: {},
    data() {
        return {
            loading: true
        }
    },

    computed: {
        ...mapGetters({
            token: 'users/token',
            user: 'users/user'
        }),
    },

    methods: {
        delete : function (id) {
            const self = this
            if (confirm("Do you really want to delete this token?")) {
                axios.delete(`/api/admin/users/${self.user.id}/tokens/${id}`).then(function (){
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'Token successfully deleted!'})
                    self.webixId.remove(id)
                })
            }
        }
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'tokensTable',
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

                    id: "last_used_at",
                    map: "(date)#last_used_at#",
                    header: ['Last used at', {content: "dateRangeFilter"}],
                    sort: "date",
                    adjust: false,
                    template: function (obj) {
                        return webix.i18n.parseFormatStr(obj.last_used_at)
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
                },

                {
                    id: "action",
                    header: ['Action', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "string",
                    css:{'text-align':'right'},
                    template: function (obj) {
                        return "<span class='btn btn-error delete'>Delete</span>"
                    }
                },
            ],
            onClick:{
                "delete" : function  (event, row, target) {
                    self.delete(row.row)
                }
            },

            url: `/api/admin/users/${self.user.id}/tokens`

        })

        Bus.$on('token:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load(`/api/admin/users/${self.user.id}/tokens`)
        });

        this.loading = false
    },

    watch: {
        user: {
            handler: function (value) {
                this.webixId.clearAll()
                this.webixId.load(`/api/admin/users/${this.user.id}/tokens`)
            },
            immediate: true
        }
    },

    destroyed:function(){
        webix.$$(this.webixId).destructor();
    }
}
</script>

<style scoped>

</style>
