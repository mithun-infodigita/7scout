<template>
    <div>
        <v-card>
            <v-card-title
            >
                Categories
                <v-spacer></v-spacer>
                <v-btn
                    v-if="!expand"
                    small
                    color="primary"
                    outlined
                    class="mr-2"
                    @click="expand = true"
                >
                    Expand
                </v-btn>
                <v-btn
                    v-if="expand"
                    small
                    color="primary"
                    outlined
                    class="mr-2"
                    @click="expand = false"
                >
                    Collapse
                </v-btn>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="showCreateCategoryDialog = true"
                >
                    <v-icon
                        color="primary"
                    left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add category
                </v-btn>
            </v-card-title>
            <v-card-text>
                <CategoriesTable></CategoriesTable>
            </v-card-text>
        </v-card>
        <CreateCategoryDialog v-bind:showDialog.sync="showCreateCategoryDialog"></CreateCategoryDialog>
    </div>
</template>

<script>
import CreateCategoryDialog from "../components/CreateCategoryDialog";
import CategoriesTable from "../components/CategoriesTable";
export default {
name: "Categories",
    components: {CategoriesTable, CreateCategoryDialog},
    data() {
        return {
            showCreateCategoryDialog: false,
            expand: false
        }
    },

    watch: {
        expand: {
            handler: function (value) {
                if($$('categoriesTable')) {
                    if (value) {
                        $$('categoriesTable').openAll()
                    } else {
                        $$('categoriesTable').closeAll()
                    }
                }
            },
            immediate: true
        },
    }
}
</script>

<style scoped>

</style>
