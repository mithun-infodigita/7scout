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
                Create import
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

                    <v-select
                        v-model="form.producer_id"
                        label="Producers"
                        :items="producers"
                        item-text="name"
                        item-value="id"
                        :error-messages="errors.producer_id"
                    >
                    </v-select>

                    <v-select
                        v-model="form.language"
                        label="Language"
                        :items="languages"
                        item-text="value"
                        item-value="key"
                        :error-messages="errors.language"
                    >
                    </v-select>
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
name: "CreateImportDialog",
    data() {
        return {
            loading: true,
            form: {
                name: '',
                producer_id: '',
                language: ''
            },
            languages: null,
            producers: null,
            errors: [],
        }
    },

    props: {
        showDialog: [Boolean, String]
    },

    computed: {
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
            setImport: 'imports/setImport'
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/imports`, this.form).then((response) => {
                self.loading = false
                self.$refs.form.reset()
                self.setImport(response.data).then(function (){
                    Bus.$emit('import:created');
                })
                self.dialog = false
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Import successfully created!'});
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

    },

    mounted() {
        const self = this

        axios.get(`/api/producers`).then((response) => {
            self.producers = response.data
        })

        axios.get(`/api/languages`).then((response) => {
            self.languages =  response.data
        })

        this.loading = false
    },
}
</script>

<style scoped>

</style>
