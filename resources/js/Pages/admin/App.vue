<template>

    <v-app>

        <v-navigation-drawer v-model="show_navigation_drawer" color="primary" v-if="config && config.is_auth">
            <v-toolbar color="appbar">
                <v-toolbar-title>Admin </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon="mdi-menu-close" @click="show_navigation_drawer = false" v-if="show_navigation_drawer" />
            </v-toolbar>
            <v-list>
                <v-list-item link title="Home" prepend-icon="mdi-home" to="/admin" />
                <v-list-item link title="Einstellungen" prepend-icon="mdi-cog" to="/admin2" />
                <v-list-item link title="Abmelden" prepend-icon="mdi-power-cycle" @click="logout" />
            </v-list>
        </v-navigation-drawer>

        <!-- Alle Dinge sind geladen -->
        <v-layout class="bg-background" v-if="config">




            <v-main>
                <router-view></router-view>
            </v-main>

            <v-footer app>
                <v-row justify="center" no-gutters>
                    <v-col cols="12" class="text-center">
                        <v-btn text variant="text">Impressum</v-btn>
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
        this.adminStore.loadConfig();
    },

    unmounted() {
    },

    data() {
        return {
            adminStore: null,
        };
    },


    computed: {
        ...mapWritableState(useAdminStore, ['config', 'is_loading', 'error', 'show_navigation_drawer', 'load_config']),
    },

    methods: {

        async logout() {
            await this.adminStore.logout();
            this.adminStore.loadConfig();
            this.$router.replace('/admin/login');
        }

    }

}
</script>
