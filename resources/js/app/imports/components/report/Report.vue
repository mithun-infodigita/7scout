<template>
    <v-row>
        <v-col
            cols="12"
            md="6"
        >
            <v-card>
                <v-card-title>
                    Selects
                </v-card-title>
                <v-card-text>
                   {{ singleImport }}
                </v-card-text>
            </v-card>
        </v-col>

        <v-col
            cols="12"
            md="6"
        >
            <v-card>
                <v-card-title>
                    Parts
                </v-card-title>
                <v-card-text>
                    Table
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import {mapGetters} from "vuex";

export default {
    name: "Report",
    computed: {
        ...mapGetters({
            singleImport: 'imports/singleImport',
        })
    },
}
</script>

<style scoped>

</style>
