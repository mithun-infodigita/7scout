<template>
    <div>
        <v-card
            :loading="loading"
        >
            <v-card-title
            >
                Columns
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="updateGroupColumns"
                >
                    Update group columns
                </v-btn>
                <v-btn
                    class="ml-2"
                    small
                    color="primary"
                    outlined
                    @click="showCreateColumnDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add column
                </v-btn>
            </v-card-title>
            <v-card-text>
                <ColumnsTable></ColumnsTable>
            </v-card-text>
        </v-card>
        <CreateColumnDialog v-bind:showDialog.sync="showCreateColumnDialog"></CreateColumnDialog>
    </div>
</template>

<script>
import CreateColumnDialog from "../components/CreateColumnDialog";
import ColumnsTable from "../components/ColumnsTable";
import {mapActions} from "vuex";
export default {
name: "Columns",
    components: {ColumnsTable, CreateColumnDialog},
    data() {
        return {
            showCreateColumnDialog: false,
            loading: false
        }
    },
    methods: {
        ...mapActions({
            runUpdateGroupColumns: 'columns/updateGroupColumns',
        }),

        updateGroupColumns() {
            const self = this
            this.loading = true
            this.runUpdateGroupColumns().then(function (){
                self.loading = false
            })
        }
    }

}
</script>

<style scoped>

</style>
