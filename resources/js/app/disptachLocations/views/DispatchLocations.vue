<template>
    <div>
        <v-card>
            <v-card-title>
                Dispatch locations
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="showCreateDispatchLocationDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add dispatch location
                </v-btn>
            </v-card-title>
            <v-card-text>
                <DispatchLocationsTable endpoint="/api/dispatchLocations"></DispatchLocationsTable>
            </v-card-text>
        </v-card>
        <CreateDispatchLocationDialog v-bind:showDialog.sync="showCreateDispatchLocationDialog"></CreateDispatchLocationDialog>
    </div>
</template>

<script>
import DispatchLocationsTable from "../components/DispatchLocationsTable";
import CreateDispatchLocationDialog from "../components/CreateDispatchLocationDialog";
export default {
    name: "DispatchLocations",
    data() {
        return {
            loading: true,
            showCreateDispatchLocationDialog: false
        }
    },
    components: {CreateDispatchLocationDialog, DispatchLocationsTable}
}
</script>

<style scoped>

</style>
