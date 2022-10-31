<template>
    <div>
        <v-card>
            <v-card-title
            >
                Facets
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="showCreateFacetDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add facet
                </v-btn>
            </v-card-title>
            <v-card-text>
                <FacetsTable></FacetsTable>
            </v-card-text>
        </v-card>
        <CreateFacetDialog v-bind:showDialog.sync="showCreateFacetDialog"></CreateFacetDialog>
    </div>
</template>

<script>
import CreateFacetDialog from "../components/CreateFacetDialog";
import FacetsTable from "../components/FacetsTable";
export default {
    name: "Facets",
    components: {FacetsTable, CreateFacetDialog},
    data () {
        return {
            showCreateFacetDialog: false
        }
    }
}
</script>

<style scoped>

</style>
