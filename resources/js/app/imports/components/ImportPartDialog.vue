<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="1200px"
        scrollable
    >
        <v-card
        :loading="loading"
        >
            <v-card-title>
                Import part {{ importPart.part_name}}
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="primary"
                    @click="dialog = false"
                >
                    <v-icon>
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col
                        cols="12"
                        md="6"
                    >

                        <v-simple-table
                            :height="height"
                        >
                            <template v-slot:default>
                                <thead>
                                <tr>
                                    <th class="text-left">
                                        Column
                                    </th>
                                    <th class="text-left">
                                        Value
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                    v-for="(value, name) in importPart"
                                    :key="name"
                                >
                                    <td>{{name}}</td>
                                    <td>{{value}}</td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-col>
                    <v-col
                        cols="12"
                        md="6"
                    >
                        <v-simple-table
                            :height="height"
                        >
                            <template v-slot:default>
                                <thead>
                                <tr>
                                    <th class="text-left">
                                        Column
                                    </th>
                                    <th class="text-left">
                                        Value
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                    v-for="(value, name) in fullRecord"

                                >
                                    <td>{{name}}</td>
                                    <td>{{value}}</td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-col>
                </v-row>

            </v-card-text>

        </v-card>
    </v-dialog>

</template>

<script>

import {mapActions, mapGetters} from 'vuex';

export default {
name: "ImportPartDialog",
    data() {
        return {
            loading: true,
            height: window.innerHeight - 200
        }
    },

    props: {
        showDialog: [Boolean, String]
    },

    computed: {
        ...mapGetters({
            importPart: 'imports/importPartData'
        }),

        fullRecord: function () {
            return JSON.parse(this.importPart.full_record)
        },


        dialog:{
            get(){
                return this.showDialog
            },
            set(val){
                return this.$emit("update:showDialog", val);
            }
        },
    },

    methods: {
        ...mapActions({

        }),



    },

    mounted() {
        const self = this


        this.loading = false
    },


    watch: {

    },
}
</script>

<style scoped>

</style>
