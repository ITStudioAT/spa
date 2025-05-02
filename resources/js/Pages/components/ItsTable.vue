<template>
    <!-- SUCHOPTIONEN -->
    <!-- nur, wenn Suchoptionen übergeben wurden -->
    <!-- nur, wenn nicht SHOW ausgewählt wurde-->
    <div class="d-flex flex-row flex-wrap align-center ga-2 mb-2 bg-secondary pa-2"
        v-if="search_options && !slots.show">
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

    <v-card flat rounded="0" :color="color ? color : 'secondary'" style="overflow: visible;">
        <!-- Überschrift -->
        <v-card-title>
            <div class="d-flex flex-row ga-2">
                <v-icon :icon="icon" v-if="icon" />
                <div>{{ title }}</div>
            </div>
        </v-card-title>

        <div v-if="!slots.show">
            <!-- Suchfeld -->
            <v-card-text>
                <v-text-field clearable density="comfortable" append-icon="mdi-magnify" hide-details
                    v-model="search_model.search_string" @keydown.enter="onSearchInput" @click:append="onSearchInput"
                    @click:clear="onSearchInput"></v-text-field>
            </v-card-text>

            <v-card-text>
                <v-list :select-strategy="multiple ? 'classic' : 'single-leaf'" style="overflow: visible;">
                    <!-- Alle Einträge -->
                    <v-list-item :value="item" v-for="(item, i) in items">
                        <v-row>
                            <v-col cols="10">
                                <v-row>
                                    <slot name="content" :item="item" />
                                </v-row>
                            </v-col>
                            <!-- Menü -->
                            <v-col cols="2" class="d-flex justify-end">
                                <v-list-item-action v-if="!!slots.actions">
                                    <v-speed-dial contained location="bottom left" transition="false">
                                        <template v-slot:activator="{ props: activatorProps }">
                                            <v-btn size="small" v-bind="activatorProps" flat icon="mdi-dots-vertical"
                                                variant="tonal"></v-btn>
                                        </template>
                                        <slot name="actions" :item="item" />
                                    </v-speed-dial>
                                </v-list-item-action>
                            </v-col>
                        </v-row>
                    </v-list-item>
                </v-list>
            </v-card-text>
        </div>

        <!-- Anzeige eines Datensatzes -->
        <v-card-text v-if="!!slots.show">
            <slot name="show" />
        </v-card-text>



    </v-card>
</template>

<script>
import { mapWritableState } from "pinia";
import { useModelStore } from "@/stores/admin/ModelStore";
import { useSlots } from 'vue';


export default {
    emits: ['saved'],
    props: ['title', 'color', 'icon', 'search_options', 'model', 'multiple', 'save_data'],

    async beforeMount() {
        this.modelStore = useModelStore(); this.modelStore.initialize(this.$router);
        this.slots = useSlots();
        await this.index(this.model, this.search_model);
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

    data() {
        return {
            search_model: {},
            modelStore: null,
            slots: null,
            selectedItem: null,

        };
    },

    watch: {
        save_data: {
            handler(newVal, oldVal) {
                this.save(this.model, newVal);
            },
            deep: true
        }

    },

    computed: {
        ...mapWritableState(useModelStore, ['items', 'pagination']),
    },


    methods: {
        async onSearchInput() {
            await this.index(this.model, this.search_model);
        },

        async index(model, search_model) {
            await this.modelStore.index(model, search_model);
        },

        async save(model, data) {
            if (data.id) {
                // update
                await this.modelStore.update(model, data);
                await this.index(model, this.search_model);
                this.$emit('saved');
            } else {
                // store
            }
        }

    }
};
</script>