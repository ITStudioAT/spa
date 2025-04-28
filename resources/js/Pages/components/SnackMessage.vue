<template>
    <v-snackbar timer="accent" v-model="is_snackbar" :timeout="my_timeout"
        :color="type == 'error' ? 'error' : 'success'" style="z-index:9000;">

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
    props: ['status', 'message', 'type', 'timeout'],

    beforeMount() {
        if (this.timeout) this.my_timeout = this.timeout;
    },


    data() {
        return {
            is_snackbar: true,
            my_timeout: 3000,
        };
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
