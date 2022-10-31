 <template>
    <v-tabs
        v-model="selectedTab"
    >
        <template>
            <v-tab href="#groupColumns">Columns</v-tab>
        </template>


        <v-tab-item value="groupColumns">
            <GroupColumnsTable></GroupColumnsTable>
        </v-tab-item>
    </v-tabs>
</template>

<script>

    import {mapGetters, mapActions} from "vuex";
    import GroupColumnsTable from "./GroupColumnsTable";

    export default {
        name: "GroupTabs",
        data() {
            return {

            }
        },
        props: {
            groupId: [String, Number]
        },
        components: {
            GroupColumnsTable

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
                setSelectedTab: 'groups/setSelectedTab'
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
