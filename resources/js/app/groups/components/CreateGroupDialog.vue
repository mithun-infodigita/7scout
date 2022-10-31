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
                Create group
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
name: "CreateGroupDialog",
    data() {
        return {
            loading: true,
            form: {
                name: '',
                parent_id: null
            },
            languages: null,
            errors: []
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
            setGroup: 'groups/setGroup',
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/groups`, this.form).then((response) => {
                self.$refs.form.reset()
                self.setGroup(response.data).then(function (){
                    Bus.$emit('group:created');
                    self.loading = false
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Group successfully created!'});
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },
    },

    mounted() {
        const self = this
        axios.get(`/api/languages`).then((response) => {
            this.languages = response.data
        })

        this.loading = false
    }
}
</script>

<style scoped>

</style>
