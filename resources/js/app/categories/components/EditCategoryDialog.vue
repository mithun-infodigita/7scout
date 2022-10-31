<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="1000px"
    >
        <v-card
            :loading="loading"
        >
            <v-card-title>
                Edit category {{category.name}} - {{ category.id}}
                <v-spacer></v-spacer>
                <v-btn
                    v-if="category.num_import_rules === 0 && category.num_children === 0"
                    text
                    color="red"
                    @click="deleteCategory"
                >
                    <v-icon>
                        mdi-trash-can-outline
                    </v-icon>
                </v-btn>
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
                    <v-col>
                        <v-form
                            ref="form"
                        >
                            <v-text-field
                                label="Name"
                                v-model="category.name"
                                :error-messages="errors.name"
                            >
                            </v-text-field>

                            <v-text-field
                                v-if="languages"
                                :key="language.key"
                                v-for="language in languages"
                                :label="language.value"
                                :value="language.key"
                                v-model="category[language.key]"
                            >
                            </v-text-field>

                            <v-autocomplete
                                v-if="categories"
                                v-model="category.parent_id"
                                :items="categories"
                                label="Parent"
                                :item-text="itemText"
                                append-outer-icon="mdi-close"
                                @click:append-outer="category.parent_id = null"
                                item-value="id"
                            >
                            </v-autocomplete>

                            <v-select
                                v-if="facets"
                                v-model="category.facet_ids"
                                multiple
                                label="Facets"
                                :items="facets"
                                item-text="name"
                                item-value="id"
                                :error-messages="errors.facet_id"
                            >
                            </v-select>
                        </v-form>

                        <v-btn
                            text
                            color="primary"
                            :disabled="loading"
                            @click="submit"
                        >
                            Store
                        </v-btn>
                    </v-col>
                    <v-col>
                        <v-card>
                            <v-card-title>
                                <h4>Image</h4>
                                <v-spacer></v-spacer>
                                <v-btn
                                    text
                                    color="red"
                                    @click="deleteImage"
                                >
                                    <v-icon>
                                        mdi-trash-can-outline
                                    </v-icon>
                                </v-btn>
                            </v-card-title>
                            <v-card-text>
                                <v-img
                                    max-width="250"
                                    contain
                                    :src="category.image_url"
                                ></v-img>
                                <file-pond
                                    class="mt-2"
                                    name="files"
                                    ref="image"
                                    :allow-revert="false"
                                    v-bind:allow-multiple="false"
                                    accepted-file-types="image/jpeg, image/png"
                                    :server="imageServer"

                                    :onprocessfiles="processCompleted"/>
                            </v-card-text>
                        </v-card>

                        <v-card>
                            <v-card-title>
                                <h4>Drawing</h4>
                                <v-spacer></v-spacer>
                                <v-btn
                                    text
                                    color="red"
                                    @click="deleteDrawing"
                                >
                                    <v-icon>
                                        mdi-trash-can-outline
                                    </v-icon>
                                </v-btn>
                            </v-card-title>
                            <v-card-text>
                                <v-img
                                    max-width="250"
                                    contain
                                    :src="category.drawing_url"
                                ></v-img>
                                <file-pond
                                    class="mt-2"
                                    name="files"
                                    ref="drawing"
                                    :allow-revert="false"
                                    v-bind:allow-multiple="false"
                                    accepted-file-types="image/jpeg, image/png"
                                    :server="drawingServer"

                                    :onprocessfiles="processCompleted"/>
                            </v-card-text>

                        </v-card>

                    </v-col>

                </v-row>
            </v-card-text>
        </v-card>
    </v-dialog>

</template>

<script>
import Bus from "../../../bus";
import vueFilePond from 'vue-filepond';

import 'filepond/dist/filepond.min.css';
import {mapActions, mapGetters} from 'vuex'

const FilePond = vueFilePond();

export default {
    name: "EditCategoryDialog",

    components: {
        FilePond
    },

    data() {
        return {
            loading: true,
            languages: null,
            facets: null,
            errors: [],
            imageServer: {
                url: `/api/categories/${this.categoryId}/images`,
                headers: {
                    'X-XSRF-TOKEN': this.$cookies.get("XSRF-TOKEN")
                },
                withCredentials: true,
            },
            drawingServer: {
                url: `/api/categories/${this.categoryId}/drawings`,
                headers: {
                    'X-XSRF-TOKEN': this.$cookies.get("XSRF-TOKEN")
                },
                withCredentials: true,
            },
        }
    },

    props: {
        showDialog: Boolean,
        categoryId: [String, Number]
    },

    computed: {
        ...mapGetters({
            categories: 'categories/categories',
            category: 'categories/category'
        }),
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
            setCategory: 'categories/setCategory',
            fetchCategories: 'categories/fetchCategories',
            fetchCategory: 'categories/fetchCategory'
        }),

        processCompleted () {
            this.fetchCategory(this.categoryId)
            this.$refs.image.removeFiles()
            this.$refs.drawing.removeFiles()
        },

        itemText(item) {
            return `${item.id} - ${item.name}`
        },

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.patch(`/api/categories/${self.category.id}`, this.category).then((response) => {
                self.setCategory(response.data).then(function (){
                    Bus.$emit('category:updated');
                    self.loading = false
                    self.dialog = false
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Category successfully updated!'});
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

        deleteCategory() {
            const self = this
            this.loading = true
            if(confirm('Do you really want to delete this category?')) {
                axios.delete(`/api/categories/${self.category.id}`).then((response) => {
                    Bus.$emit('category:updated');
                    self.loading = false
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'Category successfully deleted!'});
                    self.dialog = false
                }).catch((error) => {
                    Bus.$emit('showAlert', {color : 'error', 'message' : error.response.data.message});
                    self.loading = false
                })
            }
            self.loading = false
        },

        deleteImage() {
            const self = this
            axios.delete(`/api/categories/${self.category.id}/images/0`).then((response) => {
                this.fetchCategory(this.categoryId)
            })
        },

        deleteDrawing() {
            const self = this
            axios.delete(`/api/categories/${self.category.id}/drawings/0`).then((response) => {
                this.fetchCategory(this.categoryId)
            })
        }

    },


    mounted() {
        const self = this

        this.loading = false

        this.fetchCategories('flat')

        axios.get(`/api/languages`).then((response) => {
            this.languages = response.data

        })

        axios.get(`/api/facets`).then((response) => {
            this.facets = response.data
        })
    }
}
</script>

<style scoped>

</style>
