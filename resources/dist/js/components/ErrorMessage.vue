<template>

    <v-snackbar v-model="is_snackbar" :timeout="timeout" color="error">

        <div class="d-flex flex-row align-center ga-2">
            <v-icon :icon="icon(type)" />
            <div> {{ message + ' (' + status + ')' }}</div>
        </div>


        <template v-slot:actions>
            <v-btn variant="text" @click="is_snackbar = false">
                Schließen
            </v-btn>
        </template>
    </v-snackbar>
</template>


<script>
export default {
    props: ['status', 'message', 'timeout', 'type'],

    data() {
        return {
            is_snackbar: true,
        };
    },

    methods: {

        icon(type) {
            switch (type) {
                case 'error':
                    return 'mdi-alert-circle-outline';
                    break;
                default:
                    return 'mdi-alert-circle-outline';
            }
        },

        btnColor(type) {
            switch (type) {
                case 'error':
                    return 'error';
                    break;
                default:
                    return 'red';
            }
        }
    }

}
</script>
