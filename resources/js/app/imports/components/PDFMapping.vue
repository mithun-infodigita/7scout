<template>
<div>
    <v-card
    :loading="loading"
    >
        <v-card-text>
            <v-simple-table
                fixed-header
                :height="tableHeight"
            >
                <template v-slot:default>
                    <thead>
                        <tr>
                            <th class="text-left">
                                Map script
                            </th>
                            <th class="text-left">
                                Validation type
                            </th>
                            <th class="text-left" width="400px">
                                Validation string
                            </th>
                            <th class="text-left" width="300px">
                                PDF
                            </th>
                            <th class="text-left"  width="300px">
                                Pages
                            </th>
                            <th class="text-left" width="150px">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-if="singleImport"
                            v-for="(item, index) in singleImport.pdf_mapping"
                            :key="index"
                            :id="`row_${index}`"
                        >

                            <td>
                                <v-text-field
                                    label="Map Script"
                                    v-model="singleImport.pdf_mapping[index].map_script"
                                    :error-messages="errors[`pdf_mapping.${[index]}.map_script`] ? errors[`pdf_mapping.${[index]}.map_script`][0]: null"
                                >

                                </v-text-field>
                            </td>
                            <td>
                                <v-select
                                    label="Validation type"
                                    v-model="singleImport.pdf_mapping[index].validation_type"
                                    :error-messages="errors[`pdf_mapping.${[index]}.validation_type`] ? errors[`pdf_mapping.${[index]}.validation_type`][0]: null"
                                    :items="['equal', 'contains','between']"
                                >

                                </v-select>
                            </td>
                            <td>
                                <v-text-field
                                    label="Validation string"
                                    v-model="singleImport.pdf_mapping[index].validation_string"
                                    :error-messages="errors[`pdf_mapping.${[index]}.validation_string`] ? errors[`pdf_mapping.${[index]}.validation_string`][0]: null"
                                >

                                </v-text-field>
                            </td>
                            <td>
                                <v-select
                                    label="PDF"
                                    v-model="singleImport.pdf_mapping[index].file_name"
                                    :error-messages="errors[`pdf_mapping.${[index]}.file_name`] ? errors[`pdf_mapping.${[index]}.file_name`][0]: null"
                                    :items="singleImport.pdfs"
                                >

                                </v-select>
                            </td>
                            <td>
                                <v-text-field
                                    label="Pages"
                                    v-model="singleImport.pdf_mapping[index].pages"
                                    :error-messages="errors[`pdf_mapping.${[index]}.pages`] ? errors[`pdf_mapping.${[index]}.pages`][0]: null"
                                >

                                </v-text-field>
                            </td>
                            <td>
                                <v-row>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="copyPdfMapping(index)"
                                    >
                                        <v-icon>
                                            mdi-content-copy
                                        </v-icon>
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="error"
                                        @click="deletePdfMapping(index)"
                                    >
                                        <v-icon>
                                            mdi-trash-can-outline
                                        </v-icon>
                                    </v-btn>
                                </v-row>

                            </td>
                        </tr>


                    </tbody>
                </template>
            </v-simple-table>


        </v-card-text>
        <v-card-actions>
            <v-btn
                text
                color="primary"
                :disabled="loading"
                @click="submit"
            >
                Store
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
                class="ml-2"

                color="primary"
                text
                @click="addPdfMapping"
            >
                <v-icon
                    color="primary"
                    left
                >
                    mdi-plus-thick
                </v-icon>
                pdf mapping
            </v-btn>
        </v-card-actions>
    </v-card>
</div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Bus from "../../../bus";

export default {
    name: "PdfMapping",

    data() {
        return {
            categories: null,
            errors: [],
            loading: true,
            value: null,
            tableHeight: window.innerHeight - 300
        }
    },

    props: {
        importId: [String, Number]
    },
    computed: {
        ...mapGetters({
            singleImport: 'imports/singleImport',
        })
    },

    methods: {
        ...mapActions({
            setImport: 'imports/setImport'
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.patch(`/api/imports/${this.singleImport.id}`, this.singleImport).then((response) => {
                self.loading = false
                self.setImport(response.data).then(function (){
                    Bus.$emit('import:updated');
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Import successfully updated!'});
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },


        async addPdfMapping() {
            const self = this
            await this.addRow().then(function (){
                let numRows = self.singleImport.pdf_mapping.length

                var element = document.getElementById(`row_${numRows - 1}`);

                element.scrollIntoView()
            })
        },

        addRow() {
            return new Promise(resolve => {
                this.singleImport.pdf_mapping.push({ map_script: '$source->', validation_type: 'contains', validation_string: ''})
                resolve('resolved');

            });
        },


        deletePdfMapping(index) {
            if (confirm('Do you really want to delete this pdf mapping?')) {
               if(this.singleImport.pdf_mapping.splice(index, 1)){
                   Bus.$emit('showAlert', {color : 'success', 'message' : 'Pdf mapping successfully deleted!'});
               }
            }
        },

        async copyPdfMapping(index) {
            const self = this
            await this.copyRow(index).then(function (){
                let numRows = self.singleImport.pdf_mapping.length

                var element = document.getElementById(`row_${numRows - 1}`);

                element.scrollIntoView()
            })
        },

        copyRow(index) {
            return new Promise(resolve => {
                this.singleImport.pdf_mapping.push(this.singleImport.pdf_mapping[index])
                resolve('resolved');

            });
        },


    },

    mounted() {
        this.loading = false

        axios.get(`/api/categories?type=selectDropDown`).then((response) =>{
            this.categories = response.data
        })
    }

}
</script>

<style scoped>

</style>
