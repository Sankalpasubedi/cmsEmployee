import "../../node_modules/bootstrap/dist/css/bootstrap.css";
import { createApp } from "vue";
import App from "./App.vue";
import { createRouter, createWebHistory } from "vue-router";
import store from "./store";
import axios from "axios";
import Routes from "./routes";
import assetMixin from "./trans";
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

const router = createRouter({
    history: createWebHistory(),
    routes: Routes,
});

const token = localStorage.getItem("token");
if (token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}
const app = createApp(App);
app.use(router);
app.mixin(assetMixin);
app.use(store);
app.use(Toast, {
    position: POSITION.TOP_RIGHT,
});
app.mount("#app");
