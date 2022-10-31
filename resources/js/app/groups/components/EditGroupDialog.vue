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
                Edit group
                <v-spacer></v-spacer>
                <v-btn
                    v-if="group.num_import_rules === 0 "
                    text
                    color="red"
                    @click="deleteGroup"
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
                        v-model="group.name"
                        :error-messages="errors.name"
                    >

                    </v-text-field>
                    <v-switch
                        v-model="group.active"
                        label="Active"
                    >

                    </v-switch>

                    <v-text-field
                        :key="language.key"
                        v-for="language in languages"
                        :label="language.value"
                        :value="language.key"
                        v-model="group[language.key]"
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
name: "EditGroupDialog",
    data() {
        return {
            loading: true,
            languages: null,
            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },

    computed: {
        ...mapGetters({
            group: 'groups/group',
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
            setGroup: 'groups/setGroup',
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.patch(`/api/groups/${this.group.id}`, this.group).then((response) => {
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

        deleteGroup() {
            const self = this
            this.loading = true
            if(confirm('Do you really want to delete this group?')) {
                axios.delete(`/api/groups/${self.group.id}`).then((response) => {
                    Bus.$emit('group:updated');
                    self.loading = false
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'Group successfully deleted!'});
                    self.$router.push({name: 'groups'})
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
        axios.get(`/api/languages`).then((response) => {
            this.languages = response.data
        })
        this.loading = false
    }
}
</script>

<style scoped>

</style>
