<template>
    <div>
        <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
        ></v-progress-linear>
    </div>

</template>

<script>

import Bus from "../../../bus";
import {mapActions, mapGetters} from "vuex";
export default {

name: "CustomsSettingsTable",
    data() {
        return {
            loading: true,
            showEditCustomsSettingDialog: false,
            state: null
        }
    },

    computed: {
        ...mapGetters({
            customsSetting: 'customsSettings/customsSetting',
        }),
    },

    mounted: function () {
        const self = this

        this.webixId = webix.ui({
            container: this.$el,
            $scope: this,
            view: "datatable",
            height: window.innerHeight - 200,
            id: 'customsSettingsTable',
            headermenu: {
                width: 250,
            },
            expand:true,
            select: "row",
            headerRowHeight: 40,

            columns: [

                {
                    id: "customs_tariff_number_eu",
                    header: ['Custom tariff number EU', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "customs_tariff_number_ch",
                    header: ['Custom tariff number CH', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "import_fees_with_preferential_origin_of_goods_eu",
                    header: ['Feeswith pref. EU', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "import_fees_with_preferential_origin_of_goods_ch",
                    header: ['Fees with pref. CH', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "import_fees_without_preferential_origin_of_goods_eu",
                    header: ['Fees without pref. EU', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "import_fees_without_preferential_origin_of_goods_ch",
                    header: ['Fees without pref. CH', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "tax_unit_eu",
                    header: ['Tax units EU', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "tax_unit_ch",
                    header: ['Tax units CH', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "tax_by_value_eu",
                    header: ['Tax by values EU', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },

                {
                    id: "tax_by_value_ch",
                    header: ['Tax by values CH', {content: "textFilter"}],
                    minWidth: 50,
                    adjust: true,
                    sort: "int",
                },
            ],


            on: {
                onItemDblClick: function (row) {
                    self.$router.push({name: 'customsSettings.show', params: {customsSettingId: row.row}})
                },

                onAfterLoad: function () {
                    if (self.customsSetting && this.exists(self.customsSetting.id)) {
                        this.select(self.customsSetting.id)
                        this.showItem(self.customsSetting.id)
                    }
                },
            },

            url: '/api/customsSettings'

        })

        Bus.$on('customsSetting:created', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/customsSettings')
        });

        Bus.$on('customsSetting:updated', function () {
            self.state = self.webixId.getState()
            self.webixId.clearAll()
            self.webixId.load('/api/customsSettings')
        });

        this.loading = false
    },

    destroyed:function(){
        webix.$$(this.webixId).destructor();
    }
}
</script>

<style scoped>

</style>
