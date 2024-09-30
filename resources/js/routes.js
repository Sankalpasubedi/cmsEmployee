import Dashboard from "./components/Public/dashboard.vue";
import loginForm from "./components/Forms/loginForm.vue";
import viewPosts from "./components/Public/viewPosts.vue";
import manageAllUsers from "./components/SuperAdmin/manageAllUsers.vue";
import manageDepartmentUsers from "./components/Admin/manageDepartmentUsers.vue";
import updateProfile from "./components/Forms/updateProfile.vue";
import manageAllPosts from "./components/SuperAdmin/manageAllPosts.vue";
import manageDepartmentPosts from "./components/Admin/manageDepartmentPosts.vue";
import manageUserPost from "./components/Public/managePersonalPost.vue";
import registerForm from "./components/Forms/registerForm.vue";
import manageDepartment from "./components/SuperAdmin/manageDepartment.vue";
import resetPassword from "./components/Forms/ResetPassword.vue";

export default [
    {
        path: "/",
        component: loginForm,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem("token");
            if (token) {
                next("/dashboard");
            } else {
                next();
            }
        },
    },
    { path: "/dashboard", component: Dashboard },
    { path: "/view-posts", component: viewPosts },
    { path: "/department", component: manageDepartment },
    { path: "/manage-all-posts", component: manageAllPosts },
    { path: "/manage-department-posts", component: manageDepartmentPosts },
    { path: "/manage-all-users", component: manageAllUsers },
    { path: "/manage-department-users", component: manageDepartmentUsers },
    { path: "/manage-user-post", component: manageUserPost },
    { path: "/update-profile", component: updateProfile },
    { path: "/register", component: registerForm },
    {
        path: '/reset/:token',
        name: 'resetPassword',
        component: resetPassword,
      }
];
