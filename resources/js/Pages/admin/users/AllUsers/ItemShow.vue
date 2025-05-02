<template>
    <v-card flat rounded="0">
        <v-card-text>
            <v-form ref="form" v-model="is_valid" :disabled="!is_edit">
                <v-text-field autofocus v-model="data.last_name" label="Nachname"
                    :rules="[required(), maxLength(255)]" />
                <v-text-field v-model="data.first_name" label="Vorname" :rules="[required(), maxLength(255)]" />
                <v-text-field v-model="data.email" label="E-Mail" :rules="[required(), mail(), maxLength(255)]" />
                <v-switch true-icon="mdi-check" v-model="data.is_active" label="Aktiv" hide-details color="success"
                    :base-color="data.is_active ? 'success' : 'error'" />
                <v-switch true-icon="mdi-check" v-model="data.is_confirmed"
                    :label="data.is_confirmed ? 'Account am ' + data.confirmed_at + ' bestätigt' : 'Account bestätigt'"
                    hide-details color="success" :base-color="data.is_confirmed ? 'success' : 'error'" />
                <v-switch true-icon="mdi-check" v-model="data.is_verified"
                    :label="data.is_verified ? 'E-Mail am ' + data.email_verified_at + ' verifiziert' : 'E-Mail verifiziert'"
                    hide-details color="success" :base-color="data.is_verified ? 'success' : 'error'" disbled />
                <v-switch true-icon="mdi-check" v-model="data.is_2fa" label="2-Faktoren-Authentifizierung" hide-details
                    color="success" :base-color="data.is_2fa ? 'success' : 'error'" />
            </v-form>

            <div class="d-flex flex-row align-center ga-2 my-4 text-text" v-if="data.login_at">
                <div>Letztes Login:</div>
                <div>{{ data.login_at }}</div>
                <div>IP: {{ data.login_ip }}</div>
            </div>

            <v-row no-gutters>
                <v-col cols=12 sm=6>
                    <v-btn block color="success" slim flat rounded="0" @click="edit" v-if="!is_edit">Ändern</v-btn>
                    <v-btn block color="success" slim flat rounded="0" @click="$emit('save', data)"
                        v-if="is_edit">Speichern</v-btn>
                </v-col>
                <v-col cols=12 sm=6>
                    <v-btn block color="primary" slim flat rounded="0" @click="$emit('abortShow')"
                        v-if="!is_edit">Übersicht</v-btn>
                    <v-btn block color="error" slim flat rounded="0" @click="abortEdit" v-if="is_edit">Abbruch</v-btn>
                </v-col>
            </v-row>
        </v-card-text>

    </v-card>
</template>


<script>
import { useValidationRulesSetup } from "@/helpers/rules";

export default {
    setup() { return useValidationRulesSetup(); },

    props: ['item', 'saved'],

    async beforeMount() {
        this.data = JSON.parse(JSON.stringify(this.item));
    },

    data() {
        return {
            data: {},
            is_edit: false,
            is_valid: false,
        };
    },


    methods: {

        edit() {
            this.data = JSON.parse(JSON.stringify(this.item));
            this.is_edit = true;
        },

        abortEdit() {
            this.data = JSON.parse(JSON.stringify(this.item));
            this.is_edit = false;
        },


    }


}
</script>
