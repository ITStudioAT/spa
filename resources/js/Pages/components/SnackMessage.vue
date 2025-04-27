<template>
    <v-snackbar v-model="is_snackbar" timeout="3000" :color="type == 'error' ? 'error' : 'success'">

        <div class="d-flex flex-row align-center ga-2">
            <v-icon :icon="icon(type)" />
            <div v-if="status"> {{ message + ' (' + status + ')' }}</div>
            <div v-if="!status"> {{ message }}</div>
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
    props: ['status', 'message', 'type'],

    data() {
        return { is_snackbar: true, };
    },

    methods: {

        icon(type) {
            switch (type) {
                case 'error':
                    return 'mdi-alert-circle-outline';
                    break;
                case 'success':
                    return 'mdi-check-bold';
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
                case 'success':
                    return 'success';
                    break;
                default:
                    return 'red';
            }
        }
    }

}
</script>
