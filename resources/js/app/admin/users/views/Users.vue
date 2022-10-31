<template>
    <div>
        <v-card>
            <v-card-title
            >
                Users
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="showCreateUserDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add user
                </v-btn>
            </v-card-title>
            <v-card-text>
                <UsersTable ></UsersTable>
            </v-card-text>
        </v-card>
        <CreateUserDialog v-bind:showDialog.sync="showCreateUserDialog"></CreateUserDialog>
    </div>
</template>

<script>
import CreateUserDialog from "../components/CreateUserDialog";
import UsersTable from "../components/UsersTable";
export default {
name: "Users",
    components: {UsersTable, CreateUserDialog},
    data() {
        return {
            showCreateUserDialog: false,
        }
    }
}
</script>

<style scoped>

</style>
