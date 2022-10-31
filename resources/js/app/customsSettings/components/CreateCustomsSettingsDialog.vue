<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="900px"
    >
        <v-card
        :loading="loading"
        >
            <v-card-title>
                Create custom setting
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
                    <v-row>
                        <v-col>
                            <h4>
                                Europe
                            </h4>
                            <v-text-field
                                label="Customs tariff number"
                                v-model="form.customs_tariff_number_eu"
                                :error-messages="errors.customs_tariff_number_eu"
                            >

                            </v-text-field>

                            <v-text-field
                                label="Tax % with preferential origin of goods"
                                v-model="form.import_fees_with_preferential_origin_of_goods_eu"
                                :error-messages="errors.import_fees_with_preferential_origin_of_goods_eu"
                            >

                            </v-text-field>

                            <v-text-field
                                label="Tax % without preferential origin of goods"
                                v-model="form.import_fees_without_preferential_origin_of_goods_eu"
                                :error-messages="errors.import_fees_without_preferential_origin_of_goods_eu"
                            >

                            </v-text-field>

                            <v-select

                                v-model="form.tax_unit_eu"
                                :items="['EUR']"

                                label="Tax by unit"
                                :error-messages="errors.tax_unit_eu"
                            >

                            </v-select>
                            <v-select

                                v-model="form.tax_by_value_eu"
                                :items="[0]"
                                label="Tax by value"
                                :error-messages="errors.tax_by_value_eu"
                            >

                            </v-select>
                        </v-col>

                        <v-col>
                            <h4>
                                Switzerland
                            </h4>
                            <v-text-field
                                label="Customs tariff number"
                                v-model="form.customs_tariff_number_ch"
                                :error-messages="errors.customs_tariff_number_ch"
                            >

                            </v-text-field>

                            <v-text-field
                                label="Tax % with preferential origin of goods"
                                v-model="form.import_fees_with_preferential_origin_of_goods_ch"
                                :error-messages="errors.import_fees_with_preferential_origin_of_goods_ch"
                            >

                            </v-text-field>

                            <v-text-field
                                label="Tax % without preferential origin of goods"
                                v-model="form.import_fees_without_preferential_origin_of_goods_ch"
                                :error-messages="errors.import_fees_without_preferential_origin_of_goods_ch"
                            >

                            </v-text-field>

                            <v-select

                                v-model="form.tax_unit_ch"
                                :items="['kg']"
                                label="Tax by unit"
                                :error-messages="errors.tax_unit_ch"
                            >

                            </v-select>
                            <v-select
                                v-model="form.tax_by_value_ch"
                                :items="[100]"
                                label="Tax by value"
                                :error-messages="errors.tax_by_value_ch"
                            >

                            </v-select>
                        </v-col>

                    </v-row>
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
name: "CreateCustomsSettingsDialog",
    data() {
        return {
            loading: true,
            form: {
                customs_tariff_number_eu: 0,
                customs_tariff_number_ch: 0,
                import_fees_with_preferential_origin_of_goods_eu: 0,
                import_fees_with_preferential_origin_of_goods_ch: 0,
                import_fees_without_preferential_origin_of_goods_eu: 0,
                import_fees_without_preferential_origin_of_goods_ch: 0,
                tax_unit_eu: 'EUR',
                tax_unit_ch: 'kg',
                tax_by_value_eu: 0,
                tax_by_value_ch: 100
            },
            errors: [
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
            setCustomsSetting: 'customsSettings/setCustomsSetting',
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            axios.post(`/api/customsSettings`, this.form).then((response) => {
                self.$refs.form.reset()
                self.setCustomsSetting(response.data).then(function (){
                    Bus.$emit('setCustomsSetting:created');
                    self.loading = false
                })
                Bus.$emit('showAlert', {color : 'success', 'message' : 'Customs setting successfully created!'});
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
