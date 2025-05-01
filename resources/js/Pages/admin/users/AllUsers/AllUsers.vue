<template>
    <v-container fluid class="ma-0 w-100 pa-2">
        <!-- Menüleiste oben -->
        <v-row class="d-flex flex-row ga-2 mb-2 mt-0 w-100" no-gutters>
            <its-button subtitle="Zurück" icon="mdi-arrow-left" color="secondary" to="/admin/users" />
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
import ItsGridBox from "@/pages/components/ItsGridBox.vue";

export default {

    setup() { return useValidationRulesSetup(); },

    components: { ItsButton, ItsGridBox },

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
