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
import {mapActions, mapGetters} from "vuex";

export default {

name: "UsersTable",
    components: {},
    data() {
        return {
            loading: true,
            showEditUserDialog: false,
            state: null
        }
    },

    computed: {
        ...mapGetters({
            user: 'users/user',
        }),
    },

    methods: {
        ...mapActions({
            fetchUser: 'users/fetchUser',
        }),
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'usersTable',
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
                    id: "first_name",
                    header: ['First name', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "last_name",
                    header: ['Last name', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

                {
                    id: "email",
                    header: ['Email', {content: "textFilter"}],
                    minWidth: 150,
                    adjust: true,
                    sort: "string",
                },

            ],


            on: {
                onItemDblClick: function (row) {
                    self.$router.push(`/admin/users/${row.row}`)
                },

                onAfterLoad: function () {
                    if (self.user && this.exists(self.user.id)) {
                        this.select(self.user.id)
                        this.showItem(self.user.id)
                    }
                },
            },

            url: '/api/admin/users'

        })

        Bus.$on('user:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/admin/users')
        });

        Bus.$on('user:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/admin/users')
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
