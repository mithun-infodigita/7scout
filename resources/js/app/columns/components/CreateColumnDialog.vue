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
                Create column
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
                        v-model="form.type"
                        label="Type"
                        :items="types"
                        :error-messages="errors.type"
                    >

                    </v-select>

                    <v-switch
                        color="primary"
                        label="Nullable"
                        v-model="form.nullable"
                    >

                    </v-switch>

                    <v-switch
                        color="primary"
                        label="Import parts table"
                        v-model="form.import_parts_table"
                    >

                    </v-switch>

                    <v-switch
                        color="primary"
                        label="Index table"
                        v-model="form.index_table"
                    >

                    </v-switch>

                    <v-switch
                        color="primary"
                        label="Unique"
                        v-model="form.unique"
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
name: "CreateColumnDialog",
    data() {
        return {
            loading: true,
            form: {
                name: '',
                type: null,
                nullable: true,
                import_parts_table: true,
                index_table: true,
                unique: false
            },
            errors: [],
            types: [
                'text',
                'string',
                'boolean',
                'longtext'
            ]
        }
    },

    props: {
        showDialog: Boolean
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
            setColumn: 'columns/setColumn',
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/columns`, this.form).then((response) => {
                self.$refs.form.reset()
                self.form.nullable = true
                self.form.import_parts_table = true
                self.form.index_table = true
                self.form.unique = false
                self.setColumn(response.data).then(function (){
                    Bus.$emit('column:created');
                    self.loading = false
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Column successfully created!'});
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },
    },

    mounted() {
        const self = this

        this.loading = false
    }
}
</script>

<style scoped>

</style>
