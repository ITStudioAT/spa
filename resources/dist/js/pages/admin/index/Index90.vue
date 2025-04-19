<template>
    <v-container fluid class="h-100 w-100 d-flex align-center justify-center">
        <v-card class="mx-auto" width="300">
            <v-img height="80px" :src="'/storage/images/' + config.logo" @click="homepage" class="hover"></v-img>
            <v-card-subtitle class="text-caption text-text">
                {{ config.version }}
            </v-card-subtitle>
            <v-card-title class="mb-4" :class="titleClass(step)">
                <div v-if="['LOGIN_ENTER_EMAIL', 'LOGIN_ENTER_PASSWORD', 'LOGIN_ENTER_TOKEN'].includes(step)">Admin
                    Login</div>
                <div
                    v-if="['PASSWORD_UNKNOWN_ENTER_EMAIL', 'PASSWORD_UNKNOWN_ENTER_TOKEN', 'PASSWORD_UNKNOWN_ENTER_TOKEN_2'].includes(step)">
                    Neues Kennwort</div>
            </v-card-title>
            <!-- Login STEP LOGIN_ENTER_EMAIL = E-Mail -->
            <v-card-text v-if="step == 'LOGIN_ENTER_EMAIL'">
                <v-form ref="form" v-model="is_valid" @submit.prevent="loginStep1(data)" class="mb-4">
                    <div class="text-caption text-text">Bitte die E-Mail-Adresse eingeben</div>
                    <v-text-field autofocus v-model="data.email" label="Email" :rules="[required(), mail()]" />
                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="loginStep1(data)">Weiter</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="primary" slim flat rounded="0" variant="text" @click="passwordUnknown">Kennwort
                    unbekannt</v-btn>
            </v-card-text>

            <!-- Login STEP LOGIN_ENTER_PASSWORD = Password -->
            <v-card-text v-if="step == 'LOGIN_ENTER_PASSWORD'">
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


            <!-- Login STEP LOGIN_ENTER_TOKEN = Token_2fa -->
            <v-card-text v-if="step == 'LOGIN_ENTER_TOKEN'">
                <v-form ref="form" v-model="is_valid" @submit.prevent="loginStep3(data)" class="mb-4">
                    <v-alert closable color="success" type="info" text="Bitte prüfen Sie Ihre E-Mails" />
                    <div class="text-caption text-text">Bitte den Code laut E-Mail eingeben</div>
                    <v-otp-input autofocus v-model="data.token_2fa" />
                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="loginStep3(data)">Anmelden</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="warning" slim flat rounded="0" variant="text" @click="restartLogin">Zurück</v-btn>
            </v-card-text>


            <!-- Password unknown STEP PASSWORD_UNKNOWN_ENTER_EMAIL = E-Mail -->
            <v-card-text v-if="step == 'PASSWORD_UNKNOWN_ENTER_EMAIL'">
                <v-form ref="form" v-model="is_valid" @submit.prevent="passwordUnknownStep1(data)" class="mb-4">
                    <div class="text-caption text-text">Bitte die E-Mail-Adresse eingeben</div>
                    <v-text-field autofocus v-model="data.email" label="Email" :rules="[required(), mail()]" />
                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="passwordUnknownStep1(data)">Weiter</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="primary" slim flat rounded="0" variant="text" @click="restartLogin">Zurück zum
                    Login</v-btn>
            </v-card-text>

            <!-- Password unknown STEP PASSWORD_UNKNOWN_ENTER_TOKEN = Token -->
            <v-card-text v-if="step == 'PASSWORD_UNKNOWN_ENTER_TOKEN'">
                <v-form ref="form" v-model="is_valid" @submit.prevent="passwordUnknownStep2(data)" class="mb-4">
                    <v-alert closable color="success" type="info" text="Bitte prüfen Sie Ihre E-Mails" />
                    <div class="text-caption text-text">Bitte den Code laut E-Mail eingeben</div>
                    <v-otp-input autofocus v-model="data.token_2fa" />
                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="passwordUnknownStep2(data)">Weiter</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="warning" slim flat rounded="0" variant="text"
                    @click="restartPasswordUnknown">Zurück</v-btn>
            </v-card-text>

            <!-- Password unknown STEP PASSWORD_UNKNOWN_ENTER_TOKEN_2 = Token -->
            <v-card-text v-if="step == 'PASSWORD_UNKNOWN_ENTER_TOKEN_2'">
                <v-form ref="form" v-model="is_valid" @submit.prevent="passwordUnknownStep3(data)" class="mb-4">
                    <v-alert closable color="success" type="info"
                        text="Bitte prüfen Sie Ihre E-Mails der 2. E-Mail-Adresse" />
                    <div class="text-caption text-text">Bitte den Code laut E-Mail Ihrer 2. E-Mail-Adresse eingeben
                    </div>
                    <v-otp-input autofocus v-model="data.token_2fa_2" />
                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="passwordUnknownStep3(data)">Weiter</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="warning" slim flat rounded="0" variant="text"
                    @click="restartPasswordUnknown">Zurück</v-btn>
            </v-card-text>

            <!-- Password unknown STEP PASSWORD_UNKNOWN_ENTER_PASSWORD = Neues Kennwort erfassen -->
            <v-card-text v-if="step == 'PASSWORD_UNKNOWN_ENTER_PASSWORD'">
                <v-form ref="form" v-model="is_valid" @submit.prevent="passwordUnknownStep1(data)" class="mb-4">
                    <div class="text-caption text-text">Bitte das neue Kennwort eingeben</div>
                    <v-text-field autofocus label="Kennwort"
                        :append-icon="is_password_visible ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="is_password_visible ? 'text' : 'password'"
                        @click:append="() => (is_password_visible = !is_password_visible)"
                        :rules="[required(), minLength(8), maxLength(255)]" v-model="data.password" />
                </v-form>
                <v-btn block color="success" slim flat rounded="0" @click="passwordUnknownStep1(data)">Weiter</v-btn>
                <div class="text-caption text-center font-weight-light">oder</div>
                <v-btn block color="primary" slim flat rounded="0" variant="text" @click="restartLogin">Zurück zum
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
        this.adminStore = useAdminStore();
        this.restartLogin();

    },

    data() {
        return {
            adminStore: null,
            data: {},
            is_valid: false,
            step: null,
            is_password_visible: false,

        };
    },

    computed: {
        ...mapWritableState(useAdminStore, ['config', 'is_loading', 'error', 'api_response']),

    },

    methods: {

        titleClass(step) {
            if (['LOGIN_ENTER_EMAIL', 'LOGIN_ENTER_PASSWORD', 'LOGIN_ENTER_TOKEN'].includes(step)) {
                return 'bg-secondary';
            }

            if (['PASSWORD_UNKNOWN_ENTER_EMAIL', 'PASSWORD_UNKNOWN_ENTER_TOKEN', 'PASSWORD_UNKNOWN_ENTER_TOKEN_2', 'PASSWORD_UNKNOWN_ENTER_PASSWORD'].includes(step)) {
                return 'bg-accent';
            }
        },

        homepage() {
            window.location.href = "/";
        },

        restartLogin() {
            this.data.password = null;
            this.data.token_2fa = null;
            this.step = 'LOGIN_ENTER_EMAIL';
        },

        restartPasswordUnknown() {
            this.data.password = null;
            this.data.token_2fa = null;
            this.step = 'PASSWORD_UNKNOWN_ENTER_EMAIL';
        },

        passwordUnknown() {
            this.restartPasswordUnknown();
        },

        async loginStep1(data) {
            this.is_valid = false;
            await this.$refs.form.validate(); if (!this.is_valid) return;
            data.step = 'LOGIN_ENTER_EMAIL';
            if (await this.adminStore.loginStep1(data)) this.step = 'LOGIN_ENTER_PASSWORD';
        },

        async loginStep2(data) {
            this.is_valid = false;
            await this.$refs.form.validate(); if (!this.is_valid) return;
            data.step = 'LOGIN_ENTER_PASSWORD';
            if (await this.adminStore.loginStep2(data)) {
                if (this.api_response.data.step == 'LOGIN_SUCCESS') { this.$router.push('/admin/dashboard'); } else { this.step = 'LOGIN_ENTER_TOKEN'; }
            }
        },

        async loginStep3(data) {
            if (this.data.token_2fa.length != 6) return;
            data.step = 'LOGIN_ENTER_TOKEN';
            if (await this.adminStore.loginStep3(data)) this.$router.push('/admin/dashboard');
        },

        async passwordUnknownStep1(data) {
            this.is_valid = false;
            await this.$refs.form.validate(); if (!this.is_valid) return;
            data.step = 'PASSWORD_UNKNOWN_ENTER_EMAIL';
            if (await this.adminStore.passwordUnknownStep1(data)) this.step = 'PASSWORD_UNKNOWN_ENTER_TOKEN';
        },

        async passwordUnknownStep2(data) {
            if (this.data.token_2fa.length != 6) return;
            data.step = 'PASSWORD_UNKNOWN_ENTER_TOKEN';
            if (await this.adminStore.passwordUnknownStep2(data)) {
                if (this.api_response.data.step == 'PASSWORD_UNKNOWN_SUCCESS') { this.step = "PASSWORD_UNKNOWN_ENTER_PASSWORD" } else { this.step = 'PASSWORD_UNKNOWN_ENTER_TOKEN_2'; }
            }
        },

        async passwordUnknownStep3(data) {
            if (this.data.token_2fa.length != 6) return;
            data.step = 'PASSWORD_UNKNOWN_ENTER_TOKEN_2';
            if (await this.adminStore.passwordUnknownStep3(data)) this.step = "PASSWORD_UNKNOWN_ENTER_PASSWORD";
        }
    },


}
</script>