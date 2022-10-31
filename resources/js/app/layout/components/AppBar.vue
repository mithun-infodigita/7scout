<template>
    <div>
        <v-navigation-drawer
            v-if="authenticated"
            v-model="drawer"
            app
            :clipped="$vuetify.breakpoint.lgAndUp"
        >
            <v-list
                nav
                dense
            >
                <v-list-item-group

                    color="primary"
                >
                    <v-list-item
                        link
                        to="/columns"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-view-column</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Columns
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        link
                        to="/categories"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-format-list-text</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Categories
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        link
                        to="/groups"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-group</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Groups
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        link
                        to="/facets"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-filter-outline</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Facets
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        link
                        to="/producers"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-factory</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Producers
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        link
                        to="/dispatchLocations"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-cube-send</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Dispatch Locations
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        link
                        to="/imports"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-application-import</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Imports
                        </v-list-item-title>
                    </v-list-item>

                    <v-list-item
                        link
                        to="/partIndexes"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-database-outline</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Part Indexes
                        </v-list-item-title>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item
                        link
                        to="/customsSettings"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-cash-multiple</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Customs settings
                        </v-list-item-title>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item
                        link
                        to="/admin/users"
                    >
                        <v-list-item-icon>
                            <v-icon>  mdi-account-supervisor</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                        >
                            Users
                        </v-list-item-title>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
        </v-navigation-drawer>
        <v-app-bar
            :clipped-left="$vuetify.breakpoint.lgAndUp"
            app
            color="blue darken-3"
            dense
            dark
        >
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>7SCOUT</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn
                text
                link
                to="/login"
                v-if="!authenticated"
            >
                Login
            </v-btn>
            <v-btn
                text
                link
                @click="logout"
                v-else
            >
                Logout
            </v-btn>
        </v-app-bar>
    </div>
</template>

<script>

    import {mapActions, mapGetters} from "vuex";


    export default {
        name: "AppBar",

        data: () => ({
            drawer: true,
        }),

        computed: {
            ...mapGetters({
                authenticated: 'auth/authenticated',
            }),

        },

        methods: {
            ...mapActions({
                logout: 'auth/logout'
            }),
        },

        mounted() {

        }
    }
</script>

<style >

</style>
