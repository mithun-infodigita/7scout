<template>
    <v-card>
        <v-card-text>

            <file-pond
                class="mt-2"
                v-if="producerId"
                name="file"
                ref="pond"
                :allow-revert="false"
                v-bind:allow-multiple="true"

                :server="myServer"
                v-bind:files="uploadedFiles"
                :onprocessfiles="processCompleted"
                v-on:init="handleFilePondInit"/>

            <v-divider></v-divider>


            <v-simple-table
                fixed-header
            >
                <template v-slot:default>
                    <thead>
                    <tr>
                        <th class="text-left">
                            Name
                        </th>

                        <th class="text-right">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-if="partImages"
                        v-for="image in partImages"
                        :key="image.id"
                    >
                        <td>
                            {{ image}}
                        </td>

                        <td class="text-right">
                            <v-btn
                                color="error"
                                text
                                small
                                @click="deleteFile(image)"
                            >
                                <v-icon>
                                    mdi-trash-can-outline
                                </v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>

        </v-card-text>
    </v-card>
</template>

<script>
import vueFilePond from 'vue-filepond';

import 'filepond/dist/filepond.min.css';
import {mapActions, mapGetters} from 'vuex'

const FilePond = vueFilePond();
export default {
    name: "PartImageManager",
    components: {
        FilePond
    },

    props: {
        producerId: [String, Number]
    },

    data() {
        return {
            fileUploadDialog: false,
            errors: [],
            partImages: [],
            uploadedFiles: [],
            myServer: {
                url: `/api/producers/${this.producerId}/partImages`,
                headers: {
                    'X-XSRF-TOKEN': this.$cookies.get("XSRF-TOKEN")
                },
                withCredentials: true,
            },
            process: {


            },
        }
    },

    computed: {
        ...mapGetters({

        })
    },
    methods : {
        ...mapActions({

        }),
        cancel () {

        },
        handleFilePondInit: function() {



        },
        processCompleted () {
            this.uploadedFiles = []
            this.fetchPartImages()
        },

        fetchPartImages(){
            const self = this
            axios.get(`/api/producers/${this.producerId}/partImages`).then((response) => {
                self.partImages = response.data
            })
        },

        deleteFile(file) {
            const self = this
            if (confirm("Do you really want to delete this file?")) {
                axios.delete(`/api/producers/${this.producerId}/partImages/${file}`).then((response) => {
                    self.fetchPartImages()
                })
            }
        }

    },
    mounted() {
        const self = this
        this.fetchPartImages()
    }

}
</script>

<style scoped>

</style>

