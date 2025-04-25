<template>
    <v-sheet class="pr-2 mb-2">
        <v-card flat rounded="0" :color="color ? color : 'secondary'">
            <v-card-title>
                {{ title }}
            </v-card-title>
            <v-card-text :class="'bg-' + color + '-lighten-4'" class="pt-2">
                {{ text }}
            </v-card-text>
        </v-card>
    </v-sheet>
</template>


<script>
export default {
    props: ['title', 'text', 'color'],

    data() {
        return {

        };
    },

    methods: {

    }

}
</script>
