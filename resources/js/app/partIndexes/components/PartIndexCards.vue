<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>
        <v-card
            class="mb-5"
        >
            <template
                v-if="partIndex"
            >
                <v-card-title>
                    {{ partIndex.name}} - {{partIndex.num_indexed_parts}} parts

                    <v-spacer></v-spacer>
                        <div v-html="partIndex.status_label" class="body-2"></div>
                    <v-spacer></v-spacer>
                    <v-btn
                        small
                        color="primary"
                        outlined
                        @click="importToAlgolia"
                    >

                        Import to Algolia
                    </v-btn>
                    <v-btn
                        class="ml-2"
                        color="primary"
                        outlined
                        small
                        @click="truncateTable"
                    >
                        Truncate table
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col
                            cols="12"
                            md="4"
                        >
                            <v-simple-table
                                fixed-header
                            >
                                <thead>
                                    <tr>
                                        <th
                                            align="left"
                                        >
                                            Column
                                        </th>
                                        <th
                                            align="left"
                                        >
                                            Filled
                                        </th>
                                        <th
                                            align="left"
                                        >
                                            Empty
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            Category Level 1
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_1_filled}}
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_1_empty}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Category Level 2
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_2_filled}}
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_2_empty}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Category Level 3
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_3_filled}}
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_3_empty}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Category Level 4
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_4_filled}}
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_4_empty}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Category Level 5
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_5_filled}}
                                        </td>
                                        <td>
                                            {{partIndex.cat_level_5_empty}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Group
                                        </td>
                                        <td>
                                            {{partIndex.group_filled}}
                                        </td>
                                        <td>
                                            {{partIndex.group_empty}}
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </v-col>
                    </v-row>

                </v-card-text>
            </template>
        </v-card>
    </div>

</template>


<script>

import PartsTable from "./PartsTable";
import Bus from "../../../bus";
export default {
name: "PartIndexCards",
    components: {PartsTable},
    data () {
        return {
            loading: true,
            partIndex: null
        }
    },

    props: {
        partIndexId: [String, Number]
    },

    methods: {
        truncateTable() {
            const self = this
            this.loading = true
            if (confirm('Do you really want to truncate this table?')) {
                axios.delete(`/api/partIndexes/${this.partIndexId}/truncate`).then((response) => {
                    Bus.$emit('showAlert', {color: 'success', 'message': 'Table successfully truncated!'});
                    Bus.$emit('import:tableTruncated');
                    self.loading = false
                    self.loadPartIndex()
                })
            }
        },

        loadPartIndex() {
            const self = this
            axios.get(`/api/partIndexes/${this.partIndexId}`).then(function (response) {
                self.partIndex = response.data
                self.loading = false
            })
        },

        importToAlgolia() {
            const self = this
            self.loading = true
            axios.get(`/api/partIndexes/${this.partIndexId}/importToAlgolia`).then(function (response) {
                Bus.$emit('showAlert', {color: 'success', 'message': 'Index successfully imported to algolia!'});
                self.loading = false
            })
        }
    },

    mounted() {
        const self = this
        this.loadPartIndex()
    }
}
</script>

<style scoped>

</style>
