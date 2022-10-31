 <template>
    <v-tabs
        v-model="selectedTab"
    >

        <v-tab href="#imports">Imports</v-tab>

        <v-tab href="#dispatchLocations">Dispatch Locations</v-tab>

        <v-tab href="#partImage">Part images</v-tab>

        <v-tab href="#pdfs">PDF's</v-tab>

        <v-tab-item value="imports">
            <ImportsTable :endpoint="`/api/producers/${producerId}/imports`"></ImportsTable>
        </v-tab-item>


        <v-tab-item value="dispatchLocations">
            <DispatchLocationsTable :endpoint="`/api/producers/${producerId}/dispatchLocations`"></DispatchLocationsTable>
        </v-tab-item>


        <v-tab-item value="partImage">
            <PartImageManager :producer-id="producerId"></PartImageManager>
        </v-tab-item>

        <v-tab-item value="pdfs">
            <PDFManager  :producer-id="producerId"></PDFManager>
        </v-tab-item>
    </v-tabs>
</template>

<script>

    import {mapGetters, mapActions} from "vuex";
    import DispatchLocationsTable from "../../disptachLocations/components/DispatchLocationsTable";
    import ImportsTable from "../../imports/components/ImportsTable";
    import PartImageManager from "./PartImageManager";
    import PDFManager from "./PDFManager";


    export default {
        name: "ProducerTabs",
        data() {
            return {

            }
        },
        props: {
            producerId: [String, Number]
        },
        components: {
            PDFManager,
            PartImageManager,
            ImportsTable,
            DispatchLocationsTable


        },
        computed: {
            ...mapGetters({
                tab: 'groups/selectedTab'
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
                setSelectedTab: 'producers/setSelectedTab'
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
