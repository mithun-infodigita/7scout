<template>
    <v-card>
        <v-card-text>

            <file-pond
                class="mt-2"
                v-if="producerId"
                name="files"
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
                        v-if="pdfs"
                        v-for="pdf in pdfs"
                        :key="pdf"
                    >
                        <td>
                            {{ pdf }}
                        </td>

                        <td class="text-right">
                            <v-btn
                                color="error"
                                text
                                small
                                @click="deleteFile(pdf)"
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
    name: "PDFManager",
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
            pdfs: [],
            uploadedFiles: [],
            myServer: {
                url: `/api/producers/${this.producerId}/pdfs`,
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

        fetchPdfs(){
            const self = this
            axios.get(`/api/producers/${this.producerId}/pdfs`).then((response) => {
                self.pdfs = response.data
            })
        },

        deleteFile(pdf) {
            const self = this
            if (confirm("Do you really want to delete this file?")) {
                axios.delete(`/api/producers/${this.producerId}/pdfs/${pdf}`).then((response) => {
                    self.fetchPdfs()
                })
            }
        }

    },
    mounted() {
        const self = this
        this.fetchPdfs()
    }

}
</script>

<style scoped>

</style>

