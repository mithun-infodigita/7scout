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
                        v-model="singleImport.name"
                        :error-messages="errors.name"
                    >

                    </v-text-field>
                    <v-select
                        v-model="singleImport.producer_id"
                        label="Producers"
                        :items="producers"
                        item-text="name"
                        item-value="id"
                        :error-messages="errors.producer_id"
                    >
                    </v-select>

                    <v-select
                        v-model="singleImport.language"
                        label="Language"
                        :items="languages"
                        item-text="value"
                        item-value="key"
                        :error-messages="errors.languages"
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
name: "EditImportDialog",
    data() {
        return {
            loading: true,
            producers: null,
            partImportFiles: null,
            languages: null,
            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            singleImport: 'imports/singleImport',
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
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Import successfully changed!'});
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
    }
}
</script>

<style scoped>

</style>
