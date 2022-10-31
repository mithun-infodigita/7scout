<template>
    <div>
        <v-card>
            <v-card-title
            >
                Customs settings
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="primary"
                    outlined
                    @click="showCreateCustomsSettingDialog = true"
                >
                    <v-icon
                        color="primary"
                        left
                    >
                        mdi-plus-thick
                    </v-icon>
                    Add customs setting
                </v-btn>
            </v-card-title>
            <v-card-text>
                <CustomsSettingsTable></CustomsSettingsTable>

            </v-card-text>
        </v-card>
        <CreateCustomsSettingsDialog v-bind:showDialog.sync="showCreateCustomsSettingDialog"></CreateCustomsSettingsDialog>
    </div>
</template>

<script>
import CreateCustomsSettingsDialog from "../components/CreateCustomsSettingsDialog";
import CustomsSettingsTable from "../components/CustomsSettingsTable";
export default {
    name: "CustomsSettings",
    components: {CustomsSettingsTable, CreateCustomsSettingsDialog},
    data() {
        return {
            showCreateCustomsSettingDialog: false,
        }
    }
}
</script>

<style scoped>

</style>
