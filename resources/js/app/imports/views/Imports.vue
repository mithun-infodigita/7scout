<template>
    <div>
        <v-card>
            <v-card-title>
                Imports
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="showCreateImportDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Import
                </v-btn>
            </v-card-title>
            <v-card-text>
                <ImportsTable endpoint="/api/imports"> </ImportsTable>
            </v-card-text>
        </v-card>
        <CreateImportDialog  v-bind:showDialog.sync="showCreateImportDialog" ></CreateImportDialog>
    </div>
</template>

<script>
import ImportsTable from "../components/ImportsTable";
import CreateImportDialog from "../components/CreateImportDialog";
import Bus from "../../../bus";
export default {
    name: "Imports",
    components: {CreateImportDialog, ImportsTable},
    data() {
        return {
            showCreateImportDialog: false,
        }
    }
}
</script>

<style scoped>

</style>
