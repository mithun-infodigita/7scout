 <template>
    <v-tabs
        v-model="selectedTab"
    >
        <template>
            <v-tab href="#report"
                v-if="singleImport.type === 'map by record'"
            >
                Report
            </v-tab>

            <v-tab
                href="#importParts"
            >
                Parts
            </v-tab>

            <v-tab
                href="#basicImportFiles"
            >
                Basic Import Files
            </v-tab>

            <v-tab
                href="#oneToOne"
            >
                One to one
            </v-tab>

            <v-tab
                href="#categoryMapping"
            >
                Category mapping
            </v-tab>

            <v-tab
                href="#rules"
            >
                Mapping rules
            </v-tab>

            <v-tab
                href="#additionalFiles"
            >
                Additional files
            </v-tab>

            <v-tab
                href="#priceFiles"
            >
                Price files
            </v-tab>
            <v-tab
                href="#priceRules"
            >
                Price rules
            </v-tab>
            <v-tab
                href="#pdfMapping"
            >
                PDF Mapping
            </v-tab>
        </template>


        <v-tab-item value="report">
            <Report></Report>
        </v-tab-item>

        <v-tab-item value="importParts">
            <ImportPartsTable :import-id="importId"></ImportPartsTable>
        </v-tab-item>

        <v-tab-item value="basicImportFiles">
            <ImportBasicFiles :import-id="importId"></ImportBasicFiles>
        </v-tab-item>

        <v-tab-item value="oneToOne">
            <OneToOne :import-id="importId"></OneToOne>
        </v-tab-item>

        <v-tab-item value="categoryMapping">
            <CategoryMapping :import-id="importId"></CategoryMapping>
        </v-tab-item>

        <v-tab-item value="rules">
            <ImportRulesTable></ImportRulesTable>
        </v-tab-item>

        <v-tab-item value="additionalFiles">
            <ImportAdditionalFiles :import-id="importId"></ImportAdditionalFiles>
        </v-tab-item>

        <v-tab-item value="priceFiles">
            <ImportPriceFiles :import-id="importId"></ImportPriceFiles>
        </v-tab-item>

        <v-tab-item value="priceRules">
            <ImportPriceRulesTable></ImportPriceRulesTable>
        </v-tab-item>

        <v-tab-item value="pdfMapping">
            <PdfMapping></PdfMapping>
        </v-tab-item>
    </v-tabs>
</template>

<script>

    import {mapGetters, mapActions} from "vuex";

    import ImportRulesTable from "./ImportRulesTable";
    import Report from "./report/Report";
    import OneToOne from "./OneToOne";

    import CategoryMapping from "./CategoryMapping";
    import ImportBasicFiles from "./ImportBasicFiles";
    import ImportAdditionalFiles from "./ImportAdditionalFiles";
    import ImportPartsTable from "./ImportPartsTable";
    import ImportPriceFiles from "./ImportPriceFiles";
    import ImportPriceRulesTable from "./ImportPriceRulesTable";
    import PdfMapping from "./PDFMapping";

    export default {
        name: "ImportTabs",
        data() {
            return {

            }
        },
        props: {
            importId: [String, Number]
        },
        components: {
            PdfMapping,
            ImportPriceRulesTable,
            ImportPriceFiles,
            ImportPartsTable,
            ImportAdditionalFiles,
            ImportBasicFiles,
            CategoryMapping,
            OneToOne,
            Report,
            ImportRulesTable
        },
        computed: {
            ...mapGetters({
                tab: 'imports/selectedTab',
                singleImport: 'imports/singleImport'
            }),
            selectedTab: {
                get() {
                    return this.tab
                },
                set(value) {
                    this.setSelectedTab(value)
                }
            },
        },
        methods: {
            ...mapActions({
                setSelectedTab: 'imports/setSelectedTab'
            })
        },

        mounted() {
            this.selectedTab = this.tab
        },

        watch: {
            selectedTab: {
                handler: function (value) {
                    this.setSelectedTab(value)
                },
                immediate: true
            }
        }

    }
</script>

<style scoped>

</style>
