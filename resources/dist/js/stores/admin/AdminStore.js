import { defineStore } from 'pinia'

export const useAdminStore = defineStore("AdminStore", {

    state: () => {
        return {
            router: null,
            config: null,
            is_loading: 0,
            error: {
                is_error: false,
                status: null,
                message: null,
                timeout: 3000,
                type: 'error'
            },
            api_response: null,

        }
    },

    actions: {
        initialize(router) {
            this.router = router;
        },

        async config() {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.get("/api/admin/config", {});
                this.config = this.api_response.data;
                return true;
            } catch (error) {
                this.redirect(error.response.status, error.response.data.message, 'error', this.config.timeout);
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async registerStep1(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/register_step_1", { data });
                return true;
            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async registerStep2(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/register_step_2", { data });
                return true;
            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async registerStep3(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/register_step_3", { data });
                return true;
            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep1(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_1", { data });
                return true;
            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep2(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_2", { data });
                return true;
            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep3(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_3", { data });
                return true;
            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async passwordUnknownStep4(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/password_unknown_step_4", { data });
                return true;
            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async loginStep1(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/login_step_1", { data });
                return true;

            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },

        async loginStep2(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/login_step_2", { data });
                return true;
            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },


        async loginStep3(data) {
            this.is_loading++; this.api_response = null; this.error.is_error = false;
            try {
                this.api_response = await axios.post("/api/admin/login_step_3", { data });
                return true;
            } catch (error) {
                this.errorMsg(error.response.status, error.response.data.message, 'error', this.config.timeout ?? this.config.timeout)
                return false;
            } finally {
                this.is_loading--;
            }
        },

        redirect(status, message, type, timeout) {
            const redirectUrl = '/application/error?status=' + status + '&message=' + encodeURIComponent(message) + '&type=' + type + '&timeout=' + timeout;
            window.location.href = redirectUrl; // This is a real redirect
        },

        errorMsg(status, message, type, timeout) {
            this.error.status = status;
            this.error.message = message;
            this.error.type = type;
            this.error.timeout = timeout;
            this.error.is_error = true;
        }

    }
})

