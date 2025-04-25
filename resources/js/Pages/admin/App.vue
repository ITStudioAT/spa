<template>

    <v-app>

        <v-navigation-drawer v-model="show_navigation_drawer" color="primary" v-if="config && config.is_auth">
            <v-toolbar color="appbar">
                <v-toolbar-title>Admin</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon="mdi-menu-close" @click="show_navigation_drawer = false" v-if="show_navigation_drawer" />
            </v-toolbar>
            <v-list>
                <template v-for="(item, i) in config.menu" :key="i">
                    <v-list-item :title="item.title" :prepend-icon="item.icon" v-if="item.to" :to="item.to" />
                    <v-list-item v-if="item.click" :title="item.title" :prepend-icon="item.icon"
                        @click="() => this[item.click]()" />
                </template>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar flat color="primary">
            <template v-slot:prepend>
                <v-btn icon="mdi-menu-open" v-if="!show_navigation_drawer" @click="show_navigation_drawer = true" />
                <v-img :src="'/storage/images/logo.png'" alt="Logo" width="32" class="pl-2"></v-img>
            </template>
        </v-app-bar>



        <v-main class="bg-background" v-if="config">
            <router-view></router-view>
        </v-main>

        <v-footer app>
            <v-row justify="center" no-gutters>
                <v-col cols="12" class="text-center">
                    Fußzeile
                </v-col>
            </v-row>
        </v-footer>

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
import ErrorMessage from "@/pages/components/ErrorMessage.vue";

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
            await this.adminStore.executeLogout();
            await this.adminStore.loadConfig();
            this.$router.replace('/admin/login');
        }

    }

}
</script>
