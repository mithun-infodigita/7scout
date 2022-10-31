<template>
    <div>
        <v-card
            v-if="group"
            :loading="loading"
        >
            <v-card-title>
                Group {{groupId}} - {{group.name}}
                <v-spacer></v-spacer>
                <v-btn
                    class="mr-2"
                    color="primary"
                    outlined
                    small
                    @click="showEditGroupDialog= true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-pencil-outline
                    </v-icon>
                    Group
                </v-btn>

                <v-btn
                    v-if="selectedTab === 'groupColumns'"
                    small
                    color="primary"
                    outlined
                    @click="showCreateGroupColumnDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add group column
                </v-btn>
            </v-card-title>
            <v-card-text>
                <GroupTabs :group-id="groupId"></GroupTabs>
            </v-card-text>
            <CreateGroupColumnDialog v-bind:showDialog.sync="showCreateGroupColumnDialog"></CreateGroupColumnDialog>
            <EditGroupDialog v-bind:showDialog.sync="showEditGroupDialog" v-if="group"></EditGroupDialog>
        </v-card>

    </div>

</template>

<script>
import {mapActions, mapGetters} from "vuex";
import GroupTabs from "../components/GroupTabs";
import CreateGroupColumnDialog from "../components/CreateGroupColumnDialog";
import EditGroupDialog from "../components/EditGroupDialog";

export default {
    name: "Group",
    components: {EditGroupDialog, CreateGroupColumnDialog, GroupTabs},
    data() {
        return {
            loading: true,
            showCreateGroupColumnDialog: false,
            showEditGroupDialog: false
        }
    },

    props: {
        groupId: [Number, String]
    },

    computed: {
        ...mapGetters({
            group: 'groups/group',
            selectedTab: 'groups/selectedTab',
        })
    },

    methods: {
        ...mapActions({
            fetchGroup: 'groups/fetchGroup',
            setGroup: 'groups/setGroup'
        }),
    },

    mounted() {
        this.fetchGroup(this.groupId)
        this.loading = false
    }
}
</script>

<style scoped>

</style>
