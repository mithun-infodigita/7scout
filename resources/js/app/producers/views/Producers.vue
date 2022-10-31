<template>
    <div>
        <v-card>
            <v-card-title>
                Producers
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="showCreateProducerDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add Producer
                </v-btn>
            </v-card-title>
            <v-card-text>
                <ProducersTable></ProducersTable>
            </v-card-text>
        </v-card>
        <CreateProducerDialog v-bind:showDialog.sync="showCreateProducerDialog"></CreateProducerDialog>
    </div>
</template>

<script>
import ProducersTable from "../components/ProducersTable";
import CreateProducerDialog from "../components/CreateProducerDialog";
export default {
    name: "Producers",
    data() {
        return {
            loading: true,
            showCreateProducerDialog: false
        }
    },
    components: {CreateProducerDialog, ProducersTable}
}
</script>

<style scoped>

</style>
