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
                            <th class="text-left">
                                Validation string
                            </th>
                            <th class="text-left"                width="400px">
                                Category
                            </th>
                            <th class="text-left" >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-if="singleImport"
                            v-for="(item, index) in singleImport.category_mapping"
                            :key="index"
                            :id="`row_${index}`"
                        >

                            <td>
                                <v-text-field
                                    label="Map Script"
                                    v-model="singleImport.category_mapping[index].map_script"
                                    :error-messages="errors[`category_mapping.${[index]}.map_script`] ? errors[`category_mapping.${[index]}.map_script`][0]: null"
                                >

                                </v-text-field>
                            </td>
                            <td>
                                <v-select
                                    label="Validation type"
                                    v-model="singleImport.category_mapping[index].validation_type"
                                    :error-messages="errors[`category_mapping.${[index]}.validation_type`] ? errors[`category_mapping.${[index]}.validation_type`][0]: null"
                                    :items="['equal', 'contains','between']"
                                >

                                </v-select>
                            </td>
                            <td>
                                <v-text-field
                                    label="Validation string"
                                    v-model="singleImport.category_mapping[index].validation_string"
                                    :error-messages="errors[`category_mapping.${[index]}.validation_string`] ? errors[`category_mapping.${[index]}.validation_string`][0]: null"
                                >

                                </v-text-field>
                            </td>
                            <td>
                                <treeselect v-model="singleImport.category_mapping[index].category_id" :multiple="false" :options="categories" v-if="categories">

                                </treeselect>
                            </td>
                            <td>
                                <v-btn
                                    text
                                    color="error"
                                    @click="deleteCategoryMapping(index)"
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
                @click="addCategoryMapping"
            >
                <v-icon
                    color="primary"
                    left
                >
                    mdi-plus-thick
                </v-icon>
                category mapping
            </v-btn>
        </v-card-actions>
    </v-card>
</div>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import {mapActions, mapGetters} from "vuex";
import Bus from "../../../bus";

export default {
    name: "CategoryMapping",
    components: { Treeselect },
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


        async addCategoryMapping() {
            const self = this
            await this.addRow().then(function (){
                let numRows = self.singleImport.category_mapping.length

                var element = document.getElementById(`row_${numRows - 1}`);

                element.scrollIntoView()
            })
        },

        addRow() {
            return new Promise(resolve => {
                this.singleImport.category_mapping.push({category_id: null, map_script: '$source->', validation_type: 'contain', validation_string: ''})
                resolve('resolved');

            });
        },


deleteCategoryMapping(index) {
            if (confirm('Do you really want to delete this category mapping?')) {
               if(this.singleImport.category_mapping.splice(index, 1)){
                   Bus.$emit('showAlert', {color : 'success', 'message' : 'Category mapping successfully deleted!'});
               }
            }
        }
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
.treeSelect {

    elevation: 100
}
</style>
