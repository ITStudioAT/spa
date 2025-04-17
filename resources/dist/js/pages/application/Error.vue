<template>
    <v-empty-state headline="Whoops!" :title="message" :text="'Die Fehlernummer lautet:' + status"
        image="/storage/images/logo.png"></v-empty-state>
</template>
<script>
export default {
    async beforeMount() {
        this.status = this.$route.query.status;
        this.message = this.$route.query.message;
    },

    data() {
        return {
            status: null,
            message: null,
        };
    },


}
</script>