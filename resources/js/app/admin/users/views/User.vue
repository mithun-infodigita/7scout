<template>
    <div
        v-if="user"
    >
        <v-card>
            <v-card-title
            >
                User - {{user.full_name}}
                <v-spacer></v-spacer>
                <v-btn
                    v-if="selectedTab = 'tokens'"
                    outlined
                    small
                    color="primary"
                    @click="showCreateTokenDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add token
                </v-btn>

            </v-card-title>
            <v-card-text>
                <UserTabs></UserTabs>
            </v-card-text>
        </v-card>
        <CreateTokenDialog v-bind:showDialog.sync="showCreateTokenDialog"></CreateTokenDialog>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import UserTabs from "../components/UserTabs";
import CreateTokenDialog from "../components/CreateTokenDialog";

export default {
    name: "User",
    components: {CreateTokenDialog, UserTabs},
    data () {
        return {
            loading: true,
            showCreateTokenDialog: false
        }
    },

    props: {
        userId: [String]
    },

    computed: {
        ...mapGetters({
            user: 'users/user',
            tab: 'users/selectedTab'
        }),
        selectedTab: {
            get() {
                return this.tab
            },
            set(value) {
                this.setSelectedTab(value)
            }
        },
    },

    methods: {
        ...mapActions({
            fetchUser: 'users/fetchUser',
            setSelectedTab: 'imports/setSelectedTab'
        }),
    },

    mounted() {
        const self = this

        this.fetchUser(this.userId).then(function (){
            self.loading = false
        })
    }
}
</script>

<style scoped>

</style>
