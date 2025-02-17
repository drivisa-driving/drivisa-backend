<template>
    <v-app app>
        <v-container>
            <v-btn color="primary">
                Primary
            </v-btn>
            <v-btn color="secondary">
                Secondary
            </v-btn>
            <v-btn color="error">
                Error
            </v-btn>
        </v-container>
    </v-app>
</template>

<script>
export default {
    mounted() {
        console.log('Component mounted.')
    }
}
</script>
