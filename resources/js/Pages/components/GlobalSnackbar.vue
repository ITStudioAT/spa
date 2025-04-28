<!-- components/GlobalSnackbar.vue -->
<template>
    <v-snackbar v-model="notificationStore.show" :timeout="notificationStore.timeout" :color="notificationStore.type">
        {{ notificationStore.message }}
    </v-snackbar>
</template>

<script setup>
import { useNotificationStore } from "@/stores/spa/NotificationStore";

const notificationStore = useNotificationStore();
</script>