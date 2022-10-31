<template>
    <div>
        <v-card
            v-if="producer"
            :loading="loading"
        >
            <v-card-title>
                Producer {{producer.name}}
                <v-spacer></v-spacer>
                <v-btn
                    class="mr-2"
                    color="primary"
                    outlined
                    small
                    @click="showEditProducerDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-pencil-outline
                    </v-icon>
                    Producer
                </v-btn>


            </v-card-title>
            <v-card-text>
                <ProducerTabs :producer-id="producerId"></ProducerTabs>

            </v-card-text>

        </v-card>
        <EditProducerDialog v-bind:showDialog.sync="showEditProducerDialog" v-if="producer"></EditProducerDialog>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import EditProducerDialog from "../components/EditProducerDialog";
import ProducerTabs from "../components/ProducerTabs";

export default {
    name: "Producer",
    components: {ProducerTabs, EditProducerDialog},
    data() {
        return {
            loading: true,
            showEditProducerDialog: false
        }
    },

    props: {
        producerId: [Number, String]
    },

    computed: {
        ...mapGetters({
            producer: 'producers/producer',
        })
    },

    methods: {
        ...mapActions({
            fetchProducer: 'producers/fetchProducer'
        })

    },

    mounted() {
        this.fetchProducer(this.producerId)
        this.loading = false
    }
}
</script>

<style scoped>

</style>
