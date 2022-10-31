
<template>
    <div >
        <v-btn
            v-for="filter in filters"
            :key="filter.id"
            v-if="filter.header_button"
            :color="filter.color"
            :class="activeFilter.id === filter.id ? 'elevation-5' : ''"
            outlined
            small
            class="mr-2"
            @click="filterTable(filter)"
        >
            {{ filter.name}}
        </v-btn>

        <v-btn
            color="primary"
            outlined
            small
            class="mr-2"
            @click="resetFilter"
        >
            Reset
        </v-btn>

        <v-menu offset-y>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    small
                    outlined
                    color="primary"
                    dark
                    v-bind="attrs"
                    v-on="on"
                    class="mr-2"
                >
                    Filter
                </v-btn>
            </template>

            <v-list>
                <v-list-item-group
                    v-model="activeFilterId"
                    color="primary"
                >
                    <v-list-item
                        v-for="filter in filters"
                        :key="filter.id"
                        @click="filterTable(filter)"
                        :value="filter.id"
                    >
                        <v-list-item-title>{{filter.name}}</v-list-item-title>
                        <v-list-item-action
                            v-if="filter.default"
                        >
                            <v-list-item-action-text>
                                <v-icon
                                    color="green lighten-3"
                                >
                                    fas fa-check
                                </v-icon>
                            </v-list-item-action-text>
                        </v-list-item-action>
                    </v-list-item>
                </v-list-item-group>
                <v-divider></v-divider>
                <v-list-item
                    @click="createFilter"
                >
                    <v-list-item-title>Filter erstellen</v-list-item-title>
                </v-list-item>
                <v-list-item
                    v-if="activeFilterId"
                    @click="editFilter"
                >
                    <v-list-item-title>Filter bearbeiten</v-list-item-title>
                </v-list-item>

                <v-list-item
                    v-if="activeFilterId"
                    @click="deleteFilter"
                >
                    <v-list-item-title>Filter löschen</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>

        <v-dialog
            v-model="createFilterDialog"
            width="400"
        >
            <v-card>
                <v-card-title class="white">
                    Filter erstellen
                    <v-spacer></v-spacer>
                    <v-btn
                        small
                        text
                        color="primary"
                        @click="createFilterDialog = !createFilterDialog"
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
                            v-model="form.name"
                            label="Name"
                            :error-messages="errors.name"
                        >
                        </v-text-field>

                        <v-color-picker class="ml-0"
                                        v-model="form.color"
                                        label="Farbe"
                                        show-swatches
                                        hide-canvas
                                        hide-inputs
                                        width="400"
                        >
                        </v-color-picker>

<!--                        <v-switch-->
<!--                            v-model="form.default"-->
<!--                            label="Standard"-->
<!--                        ></v-switch>-->

                        <v-switch
                            v-model="form.header_button"
                            label="Schaltfläche"
                        ></v-switch>
                    </v-form>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-btn color="blue darken-1" text @click="save">Speichern</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="secondary" text @click="createFilterDialog = !createFilterDialog">Abbrechen</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
            v-model="editFilterDialog"
            width="400"
        >
            <v-card>
                <v-card-title class="white">
                    Filter bearbeiten
                    <v-spacer></v-spacer>
                    <v-btn
                        small
                        text
                        color="primary"
                        @click="editFilterDialog = !editFilterDialog"
                    >
                        <v-icon>
                            mdi-close
                        </v-icon>
                    </v-btn>
                </v-card-title>

                <v-card-text>
                    <v-form
                    >
                        <v-text-field
                            v-model="form.name"
                            label="Name"
                            :error-messages="errors.name"
                        >
                        </v-text-field>

                        <v-color-picker class="ml-0"
                                        v-model="form.color"
                                        label="Farbe"
                                        show-swatches
                                        hide-canvas
                                        hide-inputs
                                        width="400"
                        >
                        </v-color-picker>

<!--                        <v-switch-->
<!--                            v-model="form.default"-->
<!--                            label="Standard"-->
<!--                        ></v-switch>-->

                        <v-switch
                            v-model="form.header_button"
                            label="Schaltfläche"
                        ></v-switch>
                    </v-form>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-btn color="blue darken-1" text @click="updateFilter">Änderungen speichern</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="secondary" text @click="editFilterDialog = !editFilterDialog">Abbrechen</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="deleteFilterDialog"
            width="400"
        >
            <v-card>
                <v-card-title class="white">
                    Filter löschen
                    <v-spacer></v-spacer>
                    <v-btn
                        small
                        text
                        color="primary"
                        @click="deleteFilterDialog = !deleteFilterDialog"
                    >
                        <v-icon>
                            mdi-close
                        </v-icon>
                    </v-btn>
                </v-card-title>

                <v-card-text>
                    <strong>{{activeFilter.name}}</strong>
                    <br>
                    Wollen Sie den Filter wirklich löschen?

                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-btn color="red darken-1" text @click="destroyFilter">Ja, bitte löschen!</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="secondary" text @click="deleteFilterDialog = !deleteFilterDialog">Abbrechen</v-btn>

                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import Bus from "../../../../bus";
import {mapActions, mapGetters} from "vuex";
import {tableFilters} from "../vuex/getters";
export default {
name: "TableFilterDropDown",
    data () {
        return {
            errors: [],
            createFilterDialog: false,
            editFilterDialog: false,
            deleteFilterDialog: false,
            form: {
                name: '',
                color: '',
                default:0,
                header_button: 0,
            },
            activeFilter:'',
            activeFilterId: ''
        }
    },

    props: {
        tableId: String
    },
    computed: {
        ...mapGetters({
            tableFilters: 'tableFilters/tableFilters',
            activeFilters: 'tableFilters/activeFilters',
        }),
        filters: function () {
            if(this.tableFilters) {
                if(this.tableFilters[this.tableId]) {
                    return this.tableFilters[this.tableId]
                }
            }
        },
    },

    methods: {
        ...mapActions({
            store: 'tableFilters/store',
            update: 'tableFilters/update',
            destroy: 'tableFilters/destroy',
            fetchTableFilters: 'tableFilters/fetchTableFilters',
            setActiveFilter: 'tableFilters/setActiveFilter',
            setFilterDialog: 'tableFilters/setFilterDialog',
            unsetActiveFilter: 'tableFilters/unsetActiveFilter'
        }),

        save() {
            const self = this
            this.loading = true
            this.errors = []
            this.store({
                payload: {
                    name: this.form.name,
                    color: this.form.color,
                    default: this.form.default,
                    header_button: this.form.header_button,
                    table_id: this.tableId,
                    filter_data: $$(this.tableId).getState()
                },
                context: this
            }).then(() => {
                this.loading = false
                this.activeFilterId = this.activeFilters[self.tableId].id
                if (this.errors.length === 0) {
                    this.$refs.form.reset()
                    this.createFilterDialog = false
                    this.activeFilter = this.activeFilters[self.tableId]
                    this.activeFilterId = this.activeFilters[self.tableId].id
                }
            })
        },

        updateFilter() {
            const self = this

            this.loading = true
            this.errors = []
            this.update({
                filterId: this.activeFilter.id,
                payload: {
                    name: this.form.name,
                    color: this.form.color,
                    default: this.form.default,
                    header_button: this.form.header_button,
                    table_id: this.tableId,
                    filter_data: $$(this.tableId).getState()
                },
                context: this
            }).then(() => {
                this.loading = false
                if (this.errors.length === 0) {
                    this.editFilterDialog = false
                }
            })
        },

        destroyFilter() {
            const self = this
            this.loading = true
            this.errors = []
            this.destroy({
                filterId: this.activeFilter.id,
                context: this
            }).then(() => {
                this.loading = false
                if (this.errors.length === 0) {
                    this.deleteFilterDialog = false
                }
            })
        },

        setDefaultFilter() {
            if(this.tableFilters) {
                if(this.tableFilters[this.tableId]) {
                    this.tableFilters[this.tableId].filter((value, index) => {
                        if (value.default) {
                            this.filterTable(value)
                        }
                    })
                }
                else {
                    this.resetFilter()
                }
            }
            else {
                this.resetFilter()
            }
        },

        setFilter() {
            if(this.tableId in this.activeFilters) {
                this.filterTable(this.activeFilters[this.tableId])
            }
            else {
                this.setDefaultFilter()
            }
        },

        filterTable (filter) {

            const self = this
            Bus.$emit('filterTable:' + self.tableId, filter.filter_data );
            this.setActiveFilter(filter)
            this.activeFilter = filter
            this.activeFilterId = filter.id
            this.form = filter
        },

        resetFilter(){
            const self = this
            this.unsetActiveFilter(this.tableId)
            this.activeFilter = []
            this.activeFilterId = null
            Bus.$emit('resetFilterTable:' + self.tableId );
        },

        createFilter () {
            this.form = {
                name: '',
                color: '',
                default:'',
                header_button: '',
            },
            this.createFilterDialog = true
        },

        editFilter () {
            if(this.activeFilter) {
                this.form = this.activeFilter
                this.editFilterDialog = true
            }
        },

        deleteFilter () {
            if(this.activeFilter) {
                this.deleteFilterDialog = true
            }
        }
    },

    mounted() {

        const self = this

        if(!this.tableFilters) {
            this.fetchTableFilters().then(function (){
                self.setFilter()
            })
        }
        else {
            self.setFilter()
        }

        if(this.activeFilters[this.tableId]) {
            this.activeFilterId = this.activeFilters[this.tableId].id
        }


    }
}
</script>

<style scoped>

</style>
