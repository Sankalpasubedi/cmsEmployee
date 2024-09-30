<template>
  <div>
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
      <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
        <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
      </a>
      <a href="#" class="sidebar-toggler flex-shrink-0" style="text-decoration: none">
        <i class="fa fa-bars"></i>
      </a>
      <div class="navbar-nav align-items-center ms-auto">
        <!-- Notifications Dropdown -->
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fa fa-bell me-lg-2"></i>
            <span class="d-none d-lg-inline-flex">Notification</span>
          </a>
          <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0 p-0">
            <div v-if="notifications.length" style="
                                font-size: 0.8em;
                                width: 220px;
                                max-height: 75vh;
                                overflow-y: auto;
                            ">
              <div v-for="(notification, index) in notifications" :key="notification.id" class="dropdown-item"
                style="width: 100%; text-wrap: wrap">
                <h6 class="fw-normal mb-0 p-1" style="
                                        background-color: rgba(0, 0, 0, 0.03);
                                        width: 100%;
                                        font-size: 1em;
                                    ">
                  {{ index + 1 }}.&nbsp;{{
                    notification.data.message
                  }}
                </h6>
              </div>
              <a href="/dashboard/#notification" class="dropdown-item text-center" style="
                                    position: sticky;
                                    bottom: 0;
                                    left: 0;
                                    background-color: #0099ff;
                                    color: white;
                                    font-weight: bold;
                                ">See all notifications</a>
            </div>
            <div v-else>
              <h6 class="fw-normal mb-0 p-1 text-center" style="
                                    background-color: rgba(0, 0, 0, 0.03);
                                    width: 100%;
                                ">
                No notifications
              </h6>
            </div>
          </div>
        </div>

        <!-- User Dropdown -->
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fa-regular fa-user"></i>
            <span class="d-none d-lg-inline-flex">{{
              user?.name
            }}</span>
          </a>
          <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
            <button class="dropdown-item" @click="showUpdateProfileModal">
              Update Profile
            </button>
            <a href="#" class="dropdown-item" @click="logout">Logout</a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Update Profile Modal -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="updateProfileModalLabel">
              Update Profile
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateUser">
              <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" v-model="formData.name" class="form-control" required />
              </div>
              <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" v-model="formData.email" class="form-control" required />
              </div>
              <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="phone" v-model="formData.phone" class="form-control" />
              </div>
              <div class="row m-2 justify-content-center">
                <!-- <div class="col-4"> -->
                <button type="submit" class="btn btn-primary w-auto m-1">
                  Update
                </button>
                <!-- </div> -->
                <!-- <div class="col-4"> -->
                <button v-if="formData.role_id != 1" @click.prevent="requestSuperAdmin"
                  class="btn mydelbuttonupdate text-white w-auto m-1">
                  Request Delete
                </button>
                <!-- </div> -->
                <!-- <div class="col-4"> -->
                <button @click.prevent="resetPassword" class="myeditbuttonupdate text-white btn w-auto m-1">
                  Reset Password
                </button>
                <!-- </div> -->
              </div>
              <div v-if="loading" class="mt-3">Loading...</div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import axios from "axios";

export default {
  name: "Dashboard",
  setup() {
    const store = useStore();
    const router = useRouter();
    const user = ref(null);
    const notifications = ref([]);
    const formData = ref({
      id: "",
      name: "",
      email: "",
      phone: "",
      role_id: "1",
    });
    const loading = ref(false);

    // Fetch user data
    const fetchUserData = async () => {
      try {
        const response = await axios.get("/api/getUser");
        user.value = response.data;
        formData.value = response.data;
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    };

    // Fetch notifications
    const fetchNotifications = async (drop = 1) => {
      try {
        const response = await axios.get(
          `/api/notifications/fetch/${drop}`
        );
        notifications.value = response.data;
      } catch (error) {
        console.error("Error fetching notifications:", error);
      }
    };

    // Show the update profile modal
    const showUpdateProfileModal = () => {
      const updateProfileModal = new window.bootstrap.Modal(
        document.getElementById("updateProfileModal")
      );
      updateProfileModal.show();
    };





    // Update user
    const updateUser = async () => {
      loading.value = true;
      try {
        await axios.put(
          "/api/user/" + formData.value.id,
          formData.value
        );
        alert("User updated successfully");
        const updateProfileModal = window.bootstrap.Modal.getInstance(
          document.getElementById("updateProfileModal")
        );
        updateProfileModal.hide();
      } catch (error) {
        console.error("Error updating user:", error);
        alert("Error updating user");
      } finally {
        loading.value = false;
      }
    };

    // Request Super Admin
    const requestSuperAdmin = async () => {
      try {
        await axios.post(`/api/requestSuperAdmin/${formData.value.id}`);
        alert("Super Admin notified successfully");
      } catch (error) {
        console.error("Error notifying Super Admin:", error);
        alert("Error notifying Super Admin");
      } finally {
        loading.value = false;
      }
    };

    // Reset Password
    const resetPassword = async () => {
      loading.value = true;
      try {
        const response = await axios.post(
          "/api/password/reset-request",
          { email: formData.value.email }
        );
        alert(response.data.message);
      } catch (error) {
        console.error("Error sending reset link:", error);
        alert("Error sending reset link");
      } finally {
        loading.value = false;
      }
    };

    // Handle logout
    const logout = () => {
      store.dispatch("logout");
      router.push("/");
    };

    onMounted(() => {
      fetchUserData();
      fetchNotifications();
    });

    return {
      user,
      notifications,
      formData,
      loading,
      showUpdateProfileModal,
      updateUser,
      requestSuperAdmin,
      resetPassword,
      logout,
    };
  },
};
</script>

<style scoped>
.form-control {
  border-radius: 0.25rem;
  box-shadow: none;
}

.btn {
  margin-top: 10px;
}

.flex {
  display: flex;
}

.mt-3 {
  margin-top: 1rem;
}
</style>
