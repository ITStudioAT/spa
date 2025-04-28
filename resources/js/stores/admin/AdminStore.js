import { defineStore } from 'pinia'
import { createBaseStore } from "./BaseStore";

const baseStore = createBaseStore('users', 'user'); // <-- first create it

export const useAdminStore = defineStore("AdminAdminStore", {




    state: () => ({
        ...baseStore.state(), // merge the base state
        router: null,
        config: null,
        is_loading: 0,
        api_response: null,
        show_navigation_drawer: true,
    }),


    actions: {
        ...baseStore.actions,


        initialize(router) {
            this.router = router;
        },

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
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/register_step_1", { data });
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async registerStep2(data) {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/register_step_2", { data });
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async registerStep3(data) {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/register_step_3", { data });
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep1(data) {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_1", { data });
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep2(data) {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_2", { data });
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep3(data) {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_3", { data });
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep4(data) {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_4", { data });
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async loginStep1(data) {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/login_step_1", { data });
                return true;

            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async loginStep2(data) {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/login_step_2", { data });
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },


        async loginStep3(data) {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/login_step_3", { data });
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },


        async executeLogout() {
            this.is_loading++; this.api_response = null;
            try {
                this.api_response = await axios.post("/api/admin/execute_logout", {});
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                this.is_loading--;
            }
        },

        redirect(status, message, type) {
            const redirectUrl = '/application/error?status=' + status + '&message=' + encodeURIComponent(message) + '&type=' + type;
            window.location.href = redirectUrl; // This is a real redirect
        },

        snackMsg(status, message, type = 'error', timeout = 3000) {
            this.snack_message.status = status;
            this.snack_message.message = message;
            this.snack_message.type = type;
            this.snack_message.show = true;

            setTimeout(() => {
                this.snack_message.show = false;
            }, timeout);
        }

    }
})

