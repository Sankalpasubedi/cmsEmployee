import { createStore } from "vuex";
import axios from "axios";


const store = createStore({
    state: {
        token: localStorage.getItem("token") || "",
        role_id: null
    },
    mutations: {
        setToken(state, token) {
            state.token = token;
            localStorage.setItem("token", token);
            axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        },
        setRoleId(state, roleId) {
            state.role_id = roleId;
        },
        clearToken(state) {
            state.token = ""; 
            state.role_id = null;
            localStorage.removeItem("token");
            delete axios.defaults.headers.common["Authorization"];
        },
    },
    actions: {
        async fetchNotifications({ commit }) {
            try {
                const response = await axios.get("/api/notifications");
                commit("setNotifications", response.data);
            } catch (error) {
                console.error("Error fetching notifications:", error);
            }
        },
        async login({ commit }, credentials) {
            try {
                const response = await axios.post("/api/login", credentials);
                const token = response.data.token;
                commit("setToken", token);
                commit("setRoleId", response.data.role_id);
                return response;
            } catch (error) {
                throw error;
            }
        },
        logout({ commit }) {
            commit("clearToken");
        },
        async fetchRole({ commit }) {
            try {
                const response = await axios.get("/api/user/role");
                commit("setRoleId", response.data.role_id);
            } catch (error) {
                console.error("Error fetching user role:", error);
            }
        }
    },
});

export default store;
