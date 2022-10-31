<template>
    <v-app>
        <AppBar></AppBar>
        <v-main>
            <v-container fluid>
                <SnackBar></SnackBar>
                <router-view ></router-view>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import AppBar from "../app/layout/components/AppBar";
import SnackBar from "../app/helpers/SnackBar";
export default {
    name: "App",
    components: {SnackBar, AppBar},
}
</script>

<style scoped>

</style>
