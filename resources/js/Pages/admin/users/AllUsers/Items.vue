<template>
    <v-container fluid class="ma-0 w-100 pa-2">
        <!-- Menüleiste oben -->
        <v-row class="d-flex flex-row ga-2 mb-2 mt-0 w-100" no-gutters>
            <its-button subtitle="Benutzer" icon="mdi-arrow-left" color="secondary" to="/admin/users" />
        </v-row>

        <!-- TABELLE -->
        <v-row class="w-100" no-gutters>
            <v-col cols="12" md="6" xl="4">
                <its-table :icon="icon" color="primary"
                    :title="is_show && data ? data.last_name + ' ' + data.first_name : title"
                    :search_options="search_options" :model="this.model" :multiple="this.multiple" :data="data"
                    :save_action="save_action" :destroy_action="destroy_action" @saved="saved">

                    <template v-slot:content="{ item }">
                        <v-col cols="12" lg="6">{{ item.last_name + ' ' + item.first_name }}</v-col>
                        <v-col cols="12" lg="6">{{ item.email }}</v-col>
                    </template>

                    <!-- Menüauswahl -->
                    <template v-slot:actions="{ item }">
                        <v-btn icon="mdi-details" color="primary" @click="show(item)"></v-btn>
                        <v-btn flat icon="mdi-delete" color="error" @click="destroy(item)"></v-btn>
                        <v-btn icon="mdi-close" color="warning"></v-btn>
                    </template>

                    <!-- Anzeigen, Editieren eines Records-->
                    <template v-slot:show="{ abortShow }" v-if="is_show">
                        <item-show :item="data" @abortShow="is_show = false" @save="save" :saved="saved" />
                    </template>

                </its-table>
            </v-col>
        </v-row>

    </v-container>
</template>
<script>
import ItsButton from "@/pages/components/ItsButton.vue";
import ItsTable from "@/pages/components/ItsTable.vue";
import ItemShow from "./ItemShow.vue";

export default {

    components: { ItsButton, ItsTable, ItemShow },

    data() {
        return {
            model: 'users',
            multiple: true,
            title: 'Alle Benutzer',
            icon: 'mdi-account-group',
            /********************************************* 
             * The search_options represents the filters of the search request
             * Each search_option is displayed on the items-screen and the user may select it
            **********************************************/
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
            ],
            save_action: 0,
            destroy_action: 0,
            is_show: false,
            data: {},
            event: null,


        };
    },

    methods: {
        saved() {
            this.data = null;
            this.is_show = false;
        },
        save(data) {
            this.data = data;
            this.save_action++;
        },
        show(item) {
            this.data = item;
            this.is_show = true;
        },
        destroy(item) {
            this.data = item;
            this.destroy_action++;
        }

    }

}
</script>
