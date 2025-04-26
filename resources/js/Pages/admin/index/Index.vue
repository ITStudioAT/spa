<template>
    <v-container fluid class="ma-0 w-100 pa-2">

        <v-row class="w-100" no-gutters>
            <v-col cols="12" sm="6" md="4" xl="3">
                <its-grid-box color="primary" title="Laravel-Spa">
                    <div class="text-body-1">
                        <div>
                            Laravel-Spa dient als 'Getting in' für ein Laravel-Spa-Projekt.
                        </div>
                        <div class="mt-2">
                            Es beinhaltet das notwendige Setup, um ein Spa-Projekt direkt beginnen zu können:
                        </div>
                        <div>
                            <v-list density="compact">
                                <v-list-item density="compact">
                                    <template #prepend>
                                        <v-icon icon="mdi-check-bold" color="success" />
                                    </template>
                                    Vue mit Vue-Router als Javascript-Framework
                                </v-list-item>

                                <v-list-item density="compact">
                                    <template #prepend>
                                        <v-icon icon="mdi-check-bold" color="success" />
                                    </template>
                                    Pinia als Vue-Store
                                </v-list-item>

                                <v-list-item density="compact">
                                    <template #prepend>
                                        <v-icon icon="mdi-check-bold" color="success" />
                                    </template>
                                    Vuetify als Komponenten-Framework (CSS)
                                </v-list-item>

                                <v-list-item density="compact">
                                    <template #prepend>
                                        <v-icon icon="mdi-check-bold" color="success" />
                                    </template>
                                    Sanctum als Authentifizierungs-System
                                </v-list-item>

                                <v-list-item density="compact">
                                    <template #prepend>
                                        <v-icon icon="mdi-check-bold" color="success" />
                                    </template>
                                    Spatie Permissions für Berechtigungs-System
                                </v-list-item>

                                <v-list-item density="compact">
                                    <template #prepend>
                                        <v-icon icon="mdi-check-bold" color="success" />
                                    </template>
                                    Vite als Asset-Bundling
                                </v-list-item>

                            </v-list>
                        </div>
                    </div>
                </its-grid-box>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapWritableState } from "pinia";
import { useAdminStore } from "@/stores/admin/AdminStore";
import ItsButton from "@/pages/components/ItsButton.vue";
import ItsGridBox from "@/pages/components/ItsGridBox.vue";

export default {

    components: { ItsButton, ItsGridBox },

    async beforeMount() {
    },

    unmounted() {
    },

    data() {
        return {

        };
    },


    computed: {

    },

    methods: {


    }

}
</script>
