<template>
    <v-card>
        <v-card-text>
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
                            Size
                        </th>
                        <th class="text-right">
                            Created
                        </th>

                        <th class="text-right">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-if="files"
                        v-for="file in files"
                        :key="file.id"
                    >
                        <td>
                            <v-btn
                                color="primary"
                                text
                                :href="`/api/imports/${importId}/priceFiles/${file.id}`"
                            >
                                {{ file.name }}
                            </v-btn>
                        </td>
                        <td class="text-right">
                            {{file.size}}
                        </td>
                        <td class="text-right">
                            {{file.created_at}}
                        </td>
                        <td class="text-right">
                            <v-btn
                                color="error"
                                text
                                small
                                @click="deleteFile(file)"
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
            <v-divider></v-divider>

            <file-pond
                class="mt-2"
                v-if="singleImport"
                name="files"
                ref="pond"
                :allow-revert="false"
                v-bind:allow-multiple="true"

                :server="myServer"
                v-bind:files="uploadedFiles"
                :onprocessfiles="processCompleted"
                v-on:init="handleFilePondInit"/>
        </v-card-text>
    </v-card>
</template>

<script>
import vueFilePond from 'vue-filepond';

import 'filepond/dist/filepond.min.css';
import {mapActions, mapGetters} from 'vuex'

const FilePond = vueFilePond();
export default {
    name: "ImportPriceFiles",
    components: {
        FilePond
    },

    props: {
        importId: [String, Number]
    },

    data() {
        return {
            fileUploadDialog: false,
            errors: [],
            files: [],
            uploadedFiles: [],
            myServer: {
                url: `/api/imports/${this.importId}/priceFiles`,
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
            singleImport: 'imports/singleImport',
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
            this.fetchFiles()
        },

        fetchFiles(){
            const self = this
            axios.get(`/api/imports/${this.importId}/priceFiles`).then((response) => {
                self.files = response.data
            })
        },

        deleteFile(file) {
            const self = this
            if (confirm("Do you really want to delete this file?")) {
                axios.delete(`/api/imports/${this.importId}/priceFiles/${file.id}`).then((response) => {
                    self.fetchFiles()
                })
            }
        }

    },
    mounted() {
        const self = this
        this.fetchFiles()
    }

}
</script>

<style scoped>

</style>
