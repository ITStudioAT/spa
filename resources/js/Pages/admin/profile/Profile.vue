<template>
    <v-container fluid class="ma-0 w-100 pa-2">
        <v-row class="d-flex flex-row ga-2 mb-2 mt-0 w-100" no-gutters>
            <its-button subtitle="Home" icon="mdi-home" color="secondary" to="/admin" />
            <its-button subtitle="Kennwort ändern" icon="mdi-form-textbox-password" color="secondary"
                @click="wantToChangePassword" />
        </v-row>
        <v-row class="w-100" no-gutters>
            <v-col cols="12" sm="6" md="4" xl="3">
                <!-- PROFILDATEN ÄNDERN-->
                <its-grid-box color="primary" title="Benutzerprofil"
                    :subtitle="config.user.last_name + ' ' + config.user.first_name" icon="mdi-account"
                    v-if="step == ''">

                    <v-form ref="form" v-model="is_valid" @submit.prevent="save(data)" :disabled="!is_edit">
                        <v-text-field autofocus flat rounded="0" v-model="data.last_name" label="Nachname"
                            :rules="[required(), maxLength(255)]" />

                        <v-text-field flat rounded="0" v-model="data.first_name" label="Vorname"
                            :rules="[required(), maxLength(255)]" />

                        <v-text-field flat rounded="0" v-model="data.email" label="E-Mail"
                            :rules="[required(), mail(), maxLength(255)]" />

                    </v-form>
                    <div v-if="!is_edit">
                        <v-btn block color="primary" slim flat rounded="0" @click="is_edit = true">Ändern</v-btn>
                    </div>
                    <div v-if="is_edit">
                        <v-btn block color="success" slim flat rounded="0" @click="save(data)"
                            type="submit">Speichern</v-btn>
                        <div class="text-caption text-center font-weight-light">oder</div>
                        <v-btn block color="error" slim flat rounded="0" @click="abort">Abbruch</v-btn>
                    </div>
                </its-grid-box>

                <!-- CODE BESTÄTIGEN - BEI GEÄNDERTER E-MAIL -->
                <its-grid-box color="primary" title="Benutzerprofil"
                    :subtitle="config.user.last_name + ' ' + config.user.first_name" icon="mdi-account"
                    v-if="step == 'input_code'">

                    <div class="text-body-1">
                        <div class="mb-2">Sie beabsichtigen Ihre E-Mail-Adresse zu ändern.</div>
                        <div>Bisher: {{ api_answer.email }}</div>
                        <div class="mb-2">Neu: {{ api_answer.email_new }}</div>
                    </div>

                    <v-form ref="form" v-model="is_valid" @submit.prevent="updateWithCode(data)">
                        <v-alert closable color="success" type="info" text="Bitte prüfen Sie Ihre E-Mails" />
                        <div class="text-caption text-text">Bitte den Code laut E-Mail eingeben</div>
                        <v-otp-input autofocus v-model="data.token_2fa" />

                        <v-btn block color="success" slim flat rounded="0" @click="updateWithCode(data)"
                            type="submit">Bestätigen</v-btn>
                        <div class="text-caption text-center font-weight-light">oder</div>
                        <v-btn block color="error" slim flat rounded="0" @click="abort">Abbruch</v-btn>
                    </v-form>
                </its-grid-box>


                <!-- KENNWORT ÄNDERN-->
                <its-grid-box color="primary" title="Kennwort ändern"
                    :subtitle="config.user.last_name + ' ' + config.user.first_name" icon="mdi-account"
                    v-if="step == 'change_password'">
                    <v-form ref="form" @submit.prevent="savePassword(data)" v-model="is_valid">
                        <v-text-field autofocus label="Kennwort"
                            :append-icon="is_password_visible ? 'mdi-eye' : 'mdi-eye-off'"
                            :type="is_password_visible ? 'text' : 'password'"
                            @click:append="() => (is_password_visible = !is_password_visible)"
                            :rules="[required(), minLength(8), maxLength(255)]" v-model="data.password" />
                        <v-text-field label="Wiederholung Kennwort"
                            :append-icon="is_password_visible_repeat ? 'mdi-eye' : 'mdi-eye-off'"
                            :type="is_password_visible_repeat ? 'text' : 'password'"
                            @click:append="() => (is_password_visible_repeat = !is_password_visible_repeat)"
                            :rules="[required(), minLength(8), maxLength(255), passwordMatch(data.password)]"
                            v-model="data.password_repeat" />
                    </v-form>
                    <v-btn block color="success" slim flat rounded="0" @click="savePassword(data)"
                        type="submit">Speichern</v-btn>
                    <div class="text-caption text-center font-weight-light">oder</div>
                    <v-btn block color="error" slim flat rounded="0" @click="abort">Abbruch</v-btn>

                </its-grid-box>
            </v-col>
        </v-row>

        {{ snack_message }}
    </v-container>


</template>

<script>
import { useValidationRulesSetup } from "@/helpers/rules";
import { mapWritableState } from "pinia";
import { useAdminStore } from "@/stores/admin/AdminStore";
import { useUserStore } from "@/stores/admin/UserStore";
import ItsButton from "@/pages/components/ItsButton.vue";
import ItsGridBox from "@/pages/components/ItsGridBox.vue";

export default {

    setup() { return useValidationRulesSetup(); },

    components: { ItsButton, ItsGridBox },

    async beforeMount() {
        this.adminStore = useAdminStore(); this.adminStore.initialize(this.$router);
        this.userStore = useUserStore(); this.userStore.initialize(this.$router);

        await this.userStore.show(this.config.user.id);
        if (this.user) this.data = JSON.parse(JSON.stringify(this.user));

    },

    unmounted() {
    },

    data() {
        return {
            adminStore: null,
            userStore: null,
            is_valid: false,
            data: {},
            is_edit: false,
            step: '',
            is_password_visible: false,
            is_password_visible_repeat: false,
        };
    },

    computed: {
        ...mapWritableState(useAdminStore, ['config', 'is_loading', 'snack_message', 'show_navigation_drawer', 'load_config']),
        ...mapWritableState(useUserStore, ['user', 'api_answer']),
    },

    methods: {

        abort() {
            this.is_edit = false;
            this.step = '';
            this.data = JSON.parse(JSON.stringify(this.user));
        },

        wantToChangePassword() {
            this.abort();
            this.step = 'change_password'
        },


        async save(data) {
            await this.$refs.form.validate(); if (!this.is_valid) return;

            if (data.id) await this.update(data);
            this.is_edit = false;
            if (this.api_answer?.answer == 'input_code') {
                this.step = 'input_code';
            } else {
                this.data = JSON.parse(JSON.stringify(this.user));
                this.adminStore.snackMsg(null, "Das Profil wurde erfolreich gespeichert", 'success');
                console.log("snack");
                await this.adminStore.loadConfig();
                this.abort();
            }
        },

        async update(data) {
            await this.userStore.update(data);
        },

        async updateWithCode(data) {
            await this.$refs.form.validate(); if (!this.is_valid) return;
            if (await this.userStore.updateWithCode(data)) {
                this.adminStore.snackMsg(null, "Das Profil wurde erfolreich gespeichert", 'success');
                await this.adminStore.loadConfig();
                this.abort();
            }
        },

        async savePassword(data) {
            await this.$refs.form.validate(); if (!this.is_valid) return;
            const answer = await this.userStore.savePassword(data);
            if (answer) {
                this.step = 'enter_password_code';
            } else {
                this.abort();
            }

        }


    }

}
</script>
