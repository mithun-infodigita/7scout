<template>
    <div>
        <v-card>
            <v-card-title
            >
                Indexes
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="showCreatePartIndexDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                   Part Index
                </v-btn>
            </v-card-title>
            <v-card-text>
                <PartIndexCards v-for="partIndex in partIndexes" v-if="partIndexes" :part-index-id="partIndex.id" :key="partIndex.id"></PartIndexCards>

            </v-card-text>
        </v-card>

        <CreatePartIndexDialog v-bind:showDialog.sync="showCreatePartIndexDialog"></CreatePartIndexDialog>
    </div>
</template>

<script>

import CreatePartIndexDialog from "../components/CreatePartIndexDialog";
import PartIndexCards from "../components/PartIndexCards";
import Bus from "../../../bus";
export default {
name: "PartIndexes",
    components: {PartIndexCards, CreatePartIndexDialog},
    data() {
        return {
            loading: true,
            showCreatePartIndexDialog: false,
            partIndexes: null
        }
    },

    mounted() {
        const self = this
        axios.get('/api/partIndexes').then(function (response){
            self.partIndexes = response.data
        })

        self.loading = false

    }
}
</script>

<style scoped>

</style>
