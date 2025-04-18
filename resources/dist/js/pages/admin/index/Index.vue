<template>
    <v-container fluid class="h-100 w-100 d-flex align-center justify-center">
        <v-card class="mx-auto" width="300">
            <v-img height="80px" :src="'/storage/images/' + config.logo" @click="homepage" class="hover"></v-img>
            <v-card-subtitle class="text-caption text-text">
                {{ config.version }}
            </v-card-subtitle>
            <v-card-title class="bg-secondary mb-4">
                Admin Login
            </v-card-title>
            <v-card-text v-if="step == 0">
                <v-form ref="form" v-model="is_valid" @submit.prevent="loginStep1(data)" class="mb-4">
                    <div class="text-caption text-text">Bitte die E-Mail-Adresse eingeben</div>
                    <v-text-field autofocus v-model="data.email" label="Email" :rules="[required(), mail()]" />
                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="loginStep1(data)">Weiter</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="primary" slim flat rounded="0" variant="text">Kennwort
                    unbekannt</v-btn>
            </v-card-text>

            <v-card-text v-if="step == 1">
                <v-form ref="form" v-model="is_valid" @submit.prevent="loginStep2(data)" class="mb-4">
                    <div class="text-caption text-text">Bitte das Kennwort eingeben</div>
                    <v-text-field autofocus label="Kennwort"
                        :append-icon="is_password_visible ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="is_password_visible ? 'text' : 'password'"
                        @click:append="() => (is_password_visible = !is_password_visible)"
                        :rules="[required(), minLength(8), maxLength(255)]" v-model="data.password" />

                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="loginStep2(data)">Anmelden</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="warning" slim flat rounded="0" variant="text" @click="restartLogin">Zurück</v-btn>
            </v-card-text>


            <v-card-text v-if="step == 2">
                <v-form ref="form" v-model="is_valid" @submit.prevent="loginStep3(data)" class="mb-4">
                    <v-alert closable color="success" type="info" text="Bitte prüfen Sie Ihre E-Mails" />
                    <div class="text-caption text-text">Bitte den Code laut E-Mail eingeben</div>
                    <v-otp-input autofocus v-model="data.token_2fa" />
                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="loginStep3(data)">Anmelden</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="warning" slim flat rounded="0" variant="text" @click="restartLogin">Zurück</v-btn>
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
        this.adminStore = useAdminStore();

    },

    data() {
        return {
            adminStore: null,
            data: {},
            is_valid: false,
            step: 0,
            is_password_visible: false,

        };
    },

    computed: {
        ...mapWritableState(useAdminStore, ['config', 'is_loading', 'error', 'api_response']),

    },

    methods: {

        homepage() {
            window.location.href = "/";
        },

        restartLogin() {
            this.data.step = 0;
            this.data.password = null;
            this.data.token_2fa = null;
            this.step = 0;
        },

        async loginStep1(data) {
            this.is_valid = false;
            await this.$refs.form.validate(); if (!this.is_valid) return;
            data.step = 1;
            if (await this.adminStore.loginStep1(data)) this.step = 1;
        },

        async loginStep2(data) {
            this.is_valid = false;
            await this.$refs.form.validate(); if (!this.is_valid) return;
            data.step = 2;
            if (await this.adminStore.loginStep2(data)) {
                if (this.api_response.data.step == 0) this.$router.push('/admin/dashboard');
                this.step = 2;
            }
        },

        async loginStep3(data) {
            if (this.data.token_2fa.length != 6) return;
            data.step = 3;
            if (await this.adminStore.loginStep3(data)) this.$router.push('/admin/dashboard');
        },


    }

}
</script>