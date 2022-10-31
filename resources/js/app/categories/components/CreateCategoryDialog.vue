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
                Create category
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
                        v-model="form.translations[language.key]"
                    >

                    </v-text-field>
                    <v-autocomplete
                        v-if="categories"
                        v-model="form.parent_id"
                        :items="categories"
                        label="Parent"
                        :item-text="itemText"
                        append-outer-icon="mdi-close"
                        @click:append-outer="form.parent_id = null"
                        item-value="id"
                    >
                    </v-autocomplete>
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
name: "CreateCategoryDialog",
    data() {
        return {
            loading: true,
            form: {
                name: '',
                translations: {
                    de: ''
                },
                parent_id: 0
            },
            languages: null,
            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            categories: 'categories/categories',
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
            fetchCategories: 'categories/fetchCategories'
        }),

        itemText(item) {
            return `${item.id} - ${item.name}`
        },

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/categories`, this.form).then((response) => {
                self.$refs.form.reset()
                self.form.parent_id = 0
                self.setCategory(response.data).then(function (){
                    self.fetchCategories('flat').then(function (){
                        Bus.$emit('category:created');
                        self.loading = false
                    })
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Category successfully created!'});
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

    },

    mounted() {
        const self = this
        this.fetchCategories('flat')
        axios.get(`/api/languages`).then((response) => {
            this.languages = response.data
            _.forEach(response.data, function(value) {
                self.form.translations[value.key] = ''
            });
        })
        this.loading = false
    }
}
</script>

<style scoped>

</style>
