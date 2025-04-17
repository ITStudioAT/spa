<template>

    <v-app>
        <!-- Alle Dinge sind geladen -->
        <v-layout class="bg-background" v-if="config">
            <v-main>
                <router-view></router-view>
            </v-main>

            <v-footer app>
                <v-row justify="center" no-gutters>
                    <v-col cols="12" class="text-center">
                        <v-btn text variant="text">Impressum</v-btn>
                        {{ is_loading }}
                    </v-col>
                </v-row>

            </v-footer>
        </v-layout>

        <!-- Es wird aktuell etwas geladen-->
        <div class="d-flex justify-center align-center"
            style="position: fixed; inset: 0; background-color: rgba(255, 255, 255, 0.8); z-index: 9999;"
            v-if="is_loading > 0">
            <v-progress-circular indeterminate size="70" width="7" />
        </div>

        <ErrorMessage :status="error.status" :message="error.message" :timeout="error.timeout" :type="error.type"
            v-if="error.is_error"></ErrorMessage>
    </v-app>



</template>

<script>
import { mapWritableState } from "pinia";
import { useAdminStore } from "@/stores/admin/AdminStore";
import ErrorMessage from "@/components/ErrorMessage.vue";

export default {

    components: { ErrorMessage },

    async beforeMount() {
        this.adminStore = useAdminStore(); this.adminStore.initialize(this.$router);
        this.adminStore.config();
    },

    unmounted() {
    },

    data() {
        return {
            adminStore: null,
        };
    },

    computed: {
        ...mapWritableState(useAdminStore, ['config', 'is_loading', 'error']),

    },

    methods: {

    }

}
</script>
