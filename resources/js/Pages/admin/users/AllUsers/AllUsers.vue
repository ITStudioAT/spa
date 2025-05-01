<template>
    <v-container fluid class="ma-0 w-100 pa-2">
        <!-- Menüleiste oben -->
        <v-row class="d-flex flex-row ga-2 mb-2 mt-0 w-100" no-gutters>
            <its-button subtitle="Zurück" icon="mdi-arrow-left" color="secondary" to="/admin/users" />

        </v-row>
        <v-row class="w-100" no-gutters>
            <v-col cols="12" md="6" xl="4">
                <its-table icon="mdi-account-group" color="primary" title="Alle Benutzer"
                    :search_options="search_options" model="users"></its-table>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
import { useValidationRulesSetup } from "@/helpers/rules";
import { mapWritableState } from "pinia";
import { useAdminStore } from "@/stores/admin/AdminStore";
import { useUserStore } from "@/stores/admin/UserStore";
import { useNotificationStore } from "@/stores/spa/NotificationStore";
import ItsButton from "@/pages/components/ItsButton.vue";
import ItsTable from "@/pages/components/ItsTable.vue";

export default {

    setup() { return useValidationRulesSetup(); },

    components: { ItsButton, ItsTable },

    async beforeMount() {
        this.adminStore = useAdminStore(); this.adminStore.initialize(this.$router);
        this.userStore = useUserStore(); this.userStore.initialize(this.$router);
        await this.adminStore.managableUserRoles();

    },

    unmounted() {
    },

    data() {
        return {
            adminStore: null,
            userStore: null,
            event: null,
            search_options: [
                {
                    'type': 'toggle',
                    'field': 'is_active',
                    options: [
                        { 'name': 'Alle', 'value': 0, 'default': true },
                        { 'name': 'Aktive', 'value': 1, },
                        { 'name': 'Nicht aktive', 'value': 2 }
                    ]
                },
                {
                    'type': 'toggle',
                    'field': 'is_confirmed',
                    options: [
                        { 'name': 'Alle', 'value': 0, 'default': true },
                        { 'name': 'Bestätigte', 'value': 1, },
                        { 'name': 'Nicht Bestätigte', 'value': 2 }
                    ]
                },
                {
                    'type': 'toggle',
                    'field': 'is_verified',
                    options: [
                        { 'name': 'Alle', 'value': 0, 'default': true },
                        { 'name': 'Verifizierte', 'value': 1, },
                        { 'name': 'Nicht Verifizierte', 'value': 2 }
                    ]
                },
                {
                    'type': 'toggle',
                    'field': 'is_2fa',
                    options: [
                        { 'name': 'Alle', 'value': 0, 'default': true },
                        { 'name': 'Aktive', 'value': 1 },
                        { 'name': 'Nicht aktive', 'value': 2 }
                    ]
                },
            ]
        };
    },

    computed: {
        ...mapWritableState(useAdminStore, ['config', 'is_loading', 'show_navigation_drawer', 'load_config', 'user_roles']),
        ...mapWritableState(useUserStore, ['user', 'api_answer']),
    },

    methods: {




    }

}
</script>
