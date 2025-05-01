<template>
    <!-- SUCHOPTIONEN -->
    <div class="d-flex flex-row flex-wrap align-center ga-2 mb-2 bg-secondary pa-2" v-if="search_options">
        <div v-for="(search_option, i) in search_options" :key="i">
            <v-btn-toggle mandatory v-if="search_option.type === 'toggle'" v-model="search_model[search_option.field]"
                density="compact" divided tile @update:model-value="onSearchInput">
                <v-btn class="text-caption" v-for="(option, j) in search_option.options" :key="j" :value="option.value"
                    :color="search_model[search_option.field] === option.value ? 'success' : undefined">
                    {{ option.name }}
                </v-btn>
            </v-btn-toggle>
        </div>
    </div>

    <v-card flat rounded="0" :color="color ? color : 'secondary'">
        <v-card-title>
            <div class="d-flex flex-row ga-2">
                <v-icon :icon="icon" v-if="icon" />
                <div>{{ title }}</div>
            </div>
        </v-card-title>

        <v-card-text>
            <v-text-field clearable density="comfortable" append-icon="mdi-magnify" hide-details
                v-model="search_model.search_string" @keydown.enter="onSearchInput"
                @click:append="onSearchInput"></v-text-field>
        </v-card-text>

        <!-- Debug -->
        <pre>{{ search_model }}</pre>
    </v-card>
</template>

<script>
import { mapWritableState } from "pinia";
import { useAdminStore } from "@/stores/admin/AdminStore";
import { useModelStore } from "@/stores/admin/ModelStore";

export default {
    props: ['title', 'color', 'icon', 'search_options', 'model'],

    async beforeMount() {
        this.adminStore = useAdminStore(); this.adminStore.initialize(this.$router);
        this.modelStore = useModelStore(); this.modelStore.initialize(this.$router);
        await this.index(this.search_model);
    },

    data() {
        return {
            search_model: {},
            adminStore: null,
            modelStore: null,
        };
    },
    mounted() {
        if (this.search_options) {
            for (const searchOption of this.search_options) {
                if (searchOption.type === 'toggle') {
                    const defaultOption = searchOption.options.find((o) => o.default);
                    this.search_model[searchOption.field] = defaultOption
                        ? defaultOption.value
                        : null;
                }
            }
        }
    },
    methods: {
        async onSearchInput() {
            await this.index(this.search_model);
        },


        async index(search_model) {
            await this.modelStore.index(this.model, search_model);
        }
    }
};
</script>