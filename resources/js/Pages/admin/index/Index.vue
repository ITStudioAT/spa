<template>
    <v-container fluid class="ma-0 w-100 pa-2">
        <v-row class="d-flex flex-row ga-2 mb-2 mt-0 w-100" no-gutters>
            <its-button subtitle="Home" icon="mdi-home" color="secondary" to="/admin" />
        </v-row>
        <v-row class="w-100" no-gutters>
            <v-col cols="12" sm="6" md="4" xl="3" v-for="i in 4">
                <its-grid-box color="primary" title="Überschrift" text="Das ist ein Text" />
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
