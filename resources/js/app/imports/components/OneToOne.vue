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
                                Column
                            </th>
                            <th class="text-right">
                                Map script
                            </th>
                            <th class="text-right">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-if="singleImport"
                            v-for="(item, index) in singleImport.one_to_one"
                            :key="index"
                            :id="`row_${index}`"
                        >
                            <td>
                                <v-select
                                    v-if="columns"
                                    v-model="singleImport.one_to_one[index].column"
                                    label="Column"
                                    :items="columns"
                                    item-text="name"
                                    item-value="name"
                                    :error-messages="errors.columns"
                                >
                                </v-select>
                            </td>
                            <td>
                                <v-text-field
                                    label="Map Script"
                                    v-model="singleImport.one_to_one[index].map_script"
                                >
                                </v-text-field>
                            </td>
                            <td
                                class="text-right"
                            >
                                <v-btn
                                    text
                                    color="error"
                                    @click="deleteOneToOne(index)"
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
                @click="addOneToOne"
            >
                <v-icon
                    color="primary"
                    left
                >
                    mdi-plus-thick
                </v-icon>
                one to one
            </v-btn>
        </v-card-actions>
    </v-card>
</div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Bus from "../../../bus";

export default {
    name: "PriceMapping",
    data() {
        return {
            columns: null,
            errors: [],
            loading: true,
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

        async addOneToOne() {
            const self = this
            await this.addRow().then(function (){
                let numRows = self.singleImport.one_to_one.length

                var element = document.getElementById(`row_${numRows - 1}`);

                element.scrollIntoView()
            })
        },

        addRow() {
            return new Promise(resolve => {
                this.singleImport.one_to_one.push({column: null, map_script: '$source->'})
                resolve('resolved');

            });
        },

        deleteOneToOne(index) {
            if (confirm('Do you really want to delete this one to one?')) {
                if(this.singleImport.one_to_one.splice(index, 1)){
                    Bus.$emit('showAlert', {color : 'success', 'message' : 'One to one successfully deleted!'});
                }
            }
        }
    },

    mounted() {
        this.loading = false

        axios.get(`/api/columns`).then((response) =>{
            this.columns = response.data
        })
    }

}
</script>

<style scoped>

</style>
