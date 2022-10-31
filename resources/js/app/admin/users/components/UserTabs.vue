<template>
    <v-tabs
        v-model="selectedTab"
    >
        <template>

            <v-tab href="#tokens">Tokens</v-tab>

            <v-tab href="#resetPassword">Password</v-tab>

        </template>

        <v-tab-item value="tokens">
            <Tokens></Tokens>
        </v-tab-item>

        <v-tab-item value="resetPassword">
            <ResetPassword></ResetPassword>
        </v-tab-item>

    </v-tabs>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Tokens from "./Tokens";
import ResetPassword from "./ResetPassword";

export default {
    name: "UserTabs",
    components: {ResetPassword, Tokens},
    data() {
        return {

        }
    },

    computed: {
        ...mapGetters({
            tab: 'users/selectedTab'
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
