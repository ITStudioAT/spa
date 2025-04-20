<template>
    <v-container fluid class="h-100 w-100 d-flex align-center justify-center">
        <v-card class="mx-auto" width="300">
            <v-img height="80px" :src="'/storage/images/' + config.logo" @click="homepage" class="hover"></v-img>
            <v-card-subtitle class="text-caption text-text">
                {{ config.version }}
            </v-card-subtitle>
            <v-card-title class="mb-4 bg-success">
                Neu registrieren
            </v-card-title>

            <!-- Register STEP REGISTER_ENTER_EMAIL = E-Mail -->
            <v-card-text v-if="step == 'REGISTER_ENTER_EMAIL'">
                <v-form ref="form" v-model="is_valid" @submit.prevent="registerStep1(data)" class="mb-4">
                    <div class="text-caption text-text">Bitte die E-Mail-Adresse eingeben</div>
                    <v-text-field autofocus v-model="data.email" label="Email" :rules="[required(), mail()]" />
                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="registerStep1(data)">Weiter</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="primary" slim flat rounded="0" variant="text" @click="register">Zurück zum
                    Login</v-btn>
            </v-card-text>

        </v-card>



    </v-container>
</template>

<script>
import { useValidationRulesSetup } from "@/helpers/rules";
import { mapWritableState } from "pinia";
import { useAdminStore } from "@/stores/admin/AdminStore";

export default {

    setup() { return useValidationRulesSetup(); },

    components: {},

    async beforeMount() {
        await axios.get('/sanctum/csrf-cookie');
        this.adminStore = useAdminStore();
        this.restartRegister();

    },

    data() {
        return {
            adminStore: null,
            data: {},
            is_valid: false,
            step: null,
            is_password_visible: false,
            is_password_visible_repeat: false,

        };
    },

    computed: {
        ...mapWritableState(useAdminStore, ['config', 'is_loading', 'error', 'api_response']),

    },

    methods: {

        homepage() {
            window.location.href = "/";
        },

        login() {
            this.$router.push('/admin/login');
        },

        restartRegister() {
            this.data.password = null;
            this.data.token_2fa = null;
            this.step = 'REGISTER_ENTER_EMAIL';
        },

        register() {
            this.restartRegister();
        },

        async registerStep1(data) {
            this.is_valid = false;
            await this.$refs.form.validate(); if (!this.is_valid) return;
            data.step = 'REGISTER_ENTER_EMAIL';
            if (await this.adminStore.registerStep1(data)) this.step = 'REGISTER_ENTER_TOKEN';
        },

    },


}
</script>