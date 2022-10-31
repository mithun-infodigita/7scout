<template>
    <div>
        <v-card>
            <v-card-title
            >
                Groups
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="showCreateGroupDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add group
                </v-btn>
            </v-card-title>
            <v-card-text>
                <GroupsTable></GroupsTable>
            </v-card-text>
        </v-card>
        <CreateGroupDialog v-bind:showDialog.sync="showCreateGroupDialog"></CreateGroupDialog>
    </div>
</template>

<script>
import CreateGroupDialog from "../components/CreateGroupDialog";
import GroupsTable from "../components/GroupsTable";
export default {
name: "Groups",
    components: {GroupsTable, CreateGroupDialog},
    data() {
        return {
            showCreateGroupDialog: false,
        }
    }
}
</script>

<style scoped>

</style>
