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
                Create facet
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
                <v-form
                    ref="form"
                >
                    <v-text-field
                        label="Name"
                        v-model="form.name"
                        :error-messages="errors.name"
                    >

                    </v-text-field>

                    <v-text-field
                        :key="language.key"
                        v-for="language in languages"
                        :label="language.value"
                        :value="language.key"
                        v-model="form[language.key]"
                    >

                    </v-text-field>
                    <v-select
                        v-if="columns"
                        v-model="form.column_id"
                        :items="columns"
                        label="Column"
                        item-text="name"
                        append-outer-icon="mdi-close"
                        @click:append-outer="form.column_id = null"
                        item-value="id"
                    >
                    </v-select>
                    <v-select
                        v-model="form.item_sort"
                        :items="itemSortValues"
                        label="Item sort"
                        append-outer-icon="mdi-close"
                        @click:append-outer="facet.item_sort = null"
                    >
                    </v-select>
                    <v-switch
                        label="Global"
                        v-model="form.global_facet"
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
name: "CreateFacetDialog",
    data() {
        return {
            loading: true,
            form: {
                name: '',
                column_id: null,
                item_sort: 'text_asc',
                global_facet: false
            },
            columns: null,
            languages: null,
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
            axios.post(`/api/facets`, this.form).then((response) => {
                self.$refs.form.reset()
                self.setFacet(response.data)
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Facet successfully created!'});
                Bus.$emit('facet:created');
                self.loading = false
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

    },

    mounted() {
        const self = this
        this.fetchFacets()
        axios.get(`/api/languages`).then((response) => {
            this.languages = response.data
            _.forEach(response.data.languages, function(value) {
                self.form[value.key] = ''
            });
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
