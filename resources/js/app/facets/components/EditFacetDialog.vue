<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="600px"
    >
        <v-card
        :loading="loading"
        >
            <v-card-title>
                Edit facet
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="red"
                    @click="deleteFacet"
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
                <v-form
                    ref="form"
                >
                    <v-text-field
                        label="Name"
                        v-model="facet.name"
                        :error-messages="errors.name"
                    >

                    </v-text-field>

                    <v-text-field
                        :key="language.key"
                        v-for="language in languages"
                        :label="language.value"
                        :value="language.key"
                        v-model="facet[language.key]"
                    >

                    </v-text-field>
                    <v-select
                        v-if="columns"
                        v-model="facet.column_id"
                        :items="columns"
                        label="Column"
                        item-text="name"
                        append-outer-icon="mdi-close"
                        @click:append-outer="facet.column_id = null"
                        item-value="id"
                    >
                    </v-select>

                    <v-select
                        v-model="facet.item_sort"
                        :items="itemSortValues"
                        label="Item sort"
                        append-outer-icon="mdi-close"
                        @click:append-outer="facet.item_sort = null"
                    >
                    </v-select>

                    <v-switch
                        label="Global"
                        v-model="facet.global_facet"
                    >

                    </v-switch>
                </v-form>
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
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>

<script>
import Bus from "../../../bus";
import {mapActions, mapGetters} from 'vuex';

export default {
name: "EditFacetDialog",
    data() {
        return {
            loading: true,
            languages: null,
            columns: null,
            errors: [],
            itemSortValues: [
                'text_asc',
                'text_desc',
                'number_asc',
                'number_desc'
            ]
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            facets: 'facets/facets',
            facet: 'facets/facet'
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
            setFacet: 'facets/setFacet',
            fetchFacets: 'facets/fetchFacets'
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.patch(`/api/facets/${self.facet.id}`, this.facet).then((response) => {
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Facet successfully updated!'});
                Bus.$emit('facet:updated');
                self.loading = false
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

        deleteFacet() {
            const self = this
            this.loading = true
            if(confirm('Do you really want to delete this facet?')) {
                axios.delete(`/api/facets/${self.facet.id}`).then((response) => {
                    Bus.$emit('facet:updated');
                    self.loading = false
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'Facet successfully deleted!'});
                    self.dialog = false
                }).catch((error) => {
                    Bus.$emit('showAlert', {color : 'error', 'message' : error.response.data.message});
                    self.loading = false
                })
            }
            self.loading = false
        }
    },

    mounted() {
        const self = this
        this.fetchFacets()
        axios.get(`/api/languages`).then((response) => {
            this.languages = response.data
        })

        axios.get(`/api/columns`).then((response) => {
            this.columns = response.data
        })

        this.loading = false
    }
}
</script>

<style scoped>

</style>
