// stores/NotificationStore.js
import { defineStore } from 'pinia';

export const useNotificationStore = defineStore('notification', {
    state: () => ({
        show: false,
        message: '',
        type: 'success', // success, error, warning, info
        timeout: 3000,
    }),

    actions: {
        notify({ message, type = 'success', timeout = 3000 }) {
            this.message = message;
            this.type = type;
            this.timeout = timeout;
            this.show = true;
        }
    }
});
