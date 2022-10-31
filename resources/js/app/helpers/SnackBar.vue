<template>
    <v-snackbar
        v-model="snackbar"
        :color="color"
        centered
        :timeout="timeout"
        top
    >
        <div
            v-html="message"
        >

        </div>


        <template v-slot:action="{ attrs }">
            <v-btn
                dark
                text
                v-bind="attrs"
                @click="snackbar = false"
            >
                <v-icon>
                    mdi-close
                </v-icon>
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
    import Bus from "../../bus";

    export default {
        name: "SnackBar",

        data() {
            return {
                snackbar: false,
                color: 'success',
                message: '',
                timeout: 3000,
            }
        },

        mounted() {
            const self = this
            Bus.$on('showAlert', function (data) {
                self.color = data.color
                self.message = data.message
                self.snackbar = true
            });
        }


    }
</script>

<style scoped>

</style>
