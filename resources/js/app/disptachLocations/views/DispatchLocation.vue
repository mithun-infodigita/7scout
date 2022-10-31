<template>
    <div>
        <v-card
            v-if="dispatchLocation"
            :loading="loading"
        >
            <v-card-title>
                Dispatch location {{dispatchLocation.name}}
                <v-spacer></v-spacer>
                <v-btn
                    class="mr-2"
                    color="primary"
                    outlined
                    small
                    @click="showEditDispatchLocationDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-pencil-outline
                    </v-icon>
                    Dispatch location
                </v-btn>


            </v-card-title>
            <v-card-text>

            </v-card-text>

        </v-card>
        <EditDispatchLocationDialog v-bind:showDialog.sync="showEditDispatchLocationDialog" v-if="dispatchLocation"></EditDispatchLocationDialog>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import EditDispatchLocationDialog from "../components/EditDispatchLocationDialog";

export default {
    name: "DispatchLocation",
    components: {EditDispatchLocationDialog},
    data() {
        return {
            loading: true,
            showEditDispatchLocationDialog: false
        }
    },

    props: {
        dispatchLocationId: [Number, String]
    },

    computed: {
        ...mapGetters({
            dispatchLocation: 'dispatchLocations/dispatchLocation',
        })
    },

    methods: {
        ...mapActions({
            fetchDispatchLocation: 'dispatchLocations/fetchDispatchLocation'
        })

    },

    mounted() {
        this.fetchDispatchLocation(this.dispatchLocationId)
        this.loading = false
    }
}
</script>

<style scoped>

</style>
