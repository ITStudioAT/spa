<template>
    <v-container fluid class="ma-0 w-100 pa-2">
        <v-row class="d-flex flex-row ga-2 mb-2 mt-0 w-100" no-gutters>
            <its-button subtitle="Home" icon="mdi-home" color="secondary" to="/admin" />
            <its-button subtitle="Kennwort ändern" icon="mdi-form-textbox-password" color="secondary" to="/admin" />
        </v-row>
        <v-row class="w-100" no-gutters>
            <v-col cols="12" sm="6" md="4" xl="3">
                <!-- PROFILDATEN ÄNDERN-->
                <its-grid-box color="primary" title="Benutzerprofil"
                    :subtitle="config.user.last_name + ' ' + config.user.first_name" icon="mdi-account"
                    v-if="!is_input_code">

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
                    v-if="is_input_code">

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
            </v-col>
        </v-row>


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
            is_input_code: false,
        };
    },

    computed: {
        ...mapWritableState(useAdminStore, ['config', 'is_loading', 'error', 'show_navigation_drawer', 'load_config']),
        ...mapWritableState(useUserStore, ['user', 'api_answer']),
    },

    methods: {

        abort() {
            this.is_edit = false;
            this.is_input_code = false;
            this.data = JSON.parse(JSON.stringify(this.user));
        },

        async save(data) {
            await this.$refs.form.validate(); if (!this.is_valid) return;

            if (data.id) await this.update(data);
            this.is_edit = false;
            if (this.api_answer?.answer == 'input_code') {
                this.is_input_code = true;
            } else {
                this.data = JSON.parse(JSON.stringify(this.user));
                await this.adminStore.loadConfig();

            }
        },

        async update(data) {
            await this.userStore.update(data);
        },

        async updateWithCode(data) {
            await this.$refs.form.validate(); if (!this.is_valid) return;
            if (await this.userStore.updateWithCode(data)) {
                await this.adminStore.loadConfig();
                this.abort();
            }
        }


    }

}
</script>
