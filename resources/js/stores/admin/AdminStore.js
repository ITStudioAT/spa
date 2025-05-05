import { defineStore } from 'pinia'
import { createBaseStore } from "./BaseStore";
import { useNotificationStore } from "@/stores/spa/NotificationStore";

const baseStore = createBaseStore('users', 'user'); // <-- first create it

export const useAdminStore = defineStore("AdminAdminStore", {

    state: () => ({
        ...baseStore.state(), // merge the base state
        config: null,
        is_loading: 0,
        api_response: null,
        show_navigation_drawer: true,
        user_roles: [],
    }),


    actions: {
        ...baseStore.actions(),

        async loadConfig() {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.get("/api/admin/config", {});
                this.config = this.api_response.data;
                return this.api_response.data;
            } catch (error) {
                this.redirect(error.response.status, error.response.data.message, 'error');
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async registerStep1(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/register_step_1", { data });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async registerStep2(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/register_step_2", { data });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async registerStep3(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/register_step_3", { data });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep1(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_1", { data });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep2(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_2", { data });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep3(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_3", { data });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep4(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_4", { data });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async loginStep1(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/login_step_1", { data });
                return true;

            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async loginStep2(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/login_step_2", { data });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },


        async loginStep3(data) {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/login_step_3", { data });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },


        async executeLogout() {
            const notification = useNotificationStore();
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/execute_logout", {});
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.config?.timeout,
                });
                return false;
            } finally {
                this.is_loading--;
            }
        },




    }
})

