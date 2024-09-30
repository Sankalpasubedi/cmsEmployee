import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                "resources/css/style.css",
                "resources/js/app.js",
                "resources/js/main.js",
                "resources/css/bootstrap.min.css",
                "resources/lib/easing/easing.js",
                "resources/lib/easing/easing.min.js",
                "resources/lib/owlcarousel/owl.carousel.js",
                "resources/lib/owlcarousel/owl.carousel.min.js",
                "resources/lib/tempusdominus/css/tempusdominus-bootstrap-4.css",
                "resources/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css",
                "resources/lib/waypoints/waypoints.min.js",
                "resources/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css",
                "resources/lib/owlcarousel/assets/owl.carousel.min.css",
                "resources/lib/chart.min.js",
                "resources/lib/tempusdominus/js/moment.min.js",
                "resources/lib/moment-timezone.min.js",
                "resources/lib/tempusdominus-bootstrap-4.min.js",
                "resources/lib/",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
            "./locale": "./locale",
        },
    },
});
