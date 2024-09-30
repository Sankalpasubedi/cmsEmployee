<template>
  <div class="container-xxl position-relative bg-white col-12">
    <div v-if="isSuperadmin">
      <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa-solid fa-print fa-3x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Posts</p>
                <h6 class="mb-0">{{ totals.totalPosts }}</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa fa-chart-bar fa-3x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Personal Posts</p>
                <h6 class="mb-0">{{ totals.totalPersonalPosts }}</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa-solid fa-users fa-3x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Users</p>
                <h6 class="mb-0">{{ totals.totalUsers }}</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa-solid fa-people-roof fa-2x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Departments</p>
                <h6 class="mb-0">{{ totals.totalDepartments }}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="isAdmin">
      <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa-solid fa-print fa-3x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Department Posts</p>
                <h6 class="mb-0">{{ totals.totalDepartmentPosts }}</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa fa-chart-bar fa-3x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Personal Posts</p>
                <h6 class="mb-0">{{ totals.totalPersonalPosts }}</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa-solid fa-users fa-3x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Department Users</p>
                <h6 class="mb-0">{{ totals.totalDepartmentUsers }}</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa-solid fa-people-roof fa-2x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Department Name</p>
                <h6 class="mb-0">{{ totals.departmentName }}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa-solid fa-print fa-3x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Personal Posts</p>
                <h6 class="mb-0">{{ totals.totalPersonalPosts }}</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa fa-chart-bar fa-3x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Validated Posts</p>
                <h6 class="mb-0">{{ totals.totalValidatedPosts }}</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa-solid fa-users fa-3x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Total Department Posts</p>
                <h6 class="mb-0">{{ totals.totalDepartmentPosts }}</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3">
            <div class="bg-light dashblock rounded d-flex align-items-center justify-content-between p-4">
              <i class="fa-solid fa-people-roof fa-2x text-primary"></i>
              <div class="ms-3">
                <p class="mb-2">Department Name</p>
                <h6 class="mb-0">{{ totals.departmentName }}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid pt-4 px-4">
      <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h6 class="mb-0" id="notification">Notifications</h6>
        </div>
        <div class="table-container">
        <div class="table-responsive">
          <table class="table text-start align-middle table-bordered table-hover mb-0">
            <thead>
              <tr class="text-dark">
                <th scope="col">#</th>
                <th scope="col">Type</th>
                <th scope="col">Notification</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(notification, index) in notifications" :key="notification.id">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ formatNotificationType(notification.type) }}</td>
                <td>{{ notification.data.message }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "Dashboard",
  data() {
    return {
      user: null,
      notifications: [],
      isSuperadmin: false,
      isAdmin: false,
      isUser: false,
      totals: {
        totalPosts: 0,
        totalPersonalPosts: 0,
        totalUsers: 0,
        totalDepartments: 0,
        totalDepartmentPosts: 0,
        totalDepartmentUsers: 0,
        departmentName: '',
        totalValidatedPosts: 0,
      },
    };
  },
  created() {
    this.fetchUserData();
    this.fetchNotifications();
    this.fetchTotals();
  },
  methods: {
    async fetchTotals() {
      try {
        const response = await axios.get('/api/fetchTotals');
        this.totals = response.data;
      } catch (error) {
        console.error("Error fetching totals:", error);
      }
    },
    async fetchUserData() {
      try {
        const response = await axios.get('/api/getUser');
        this.user = response.data;
        if (response.data.role_id === 1) {
          this.isSuperadmin = true;
        } else if (response.data.role_id === 2) {
          this.isAdmin = true;
        } else if (response.data.role_id === 3) {
          this.isUser = true;
        }
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    },
    async fetchNotifications() {
      try {
        const response = await axios.get(`/api/notifications/fetch/${0}`);
        this.notifications = response.data;
      } catch (error) {
        console.error("Error fetching notifications:", error);
      }
    },
    formatNotificationType(type) {
      return type.replace('App\\Notifications\\', '');
    },
  },
};
</script>

<style scoped>
.dashboard {
  display: flex;
}

.content {
  flex: 1;
  padding: 20px;
}

.table-container {
  max-height: 288px;
  overflow-y: auto;
}

.table {
  margin-bottom: 0;
}
.dashblock
{
  height: 130px;
}
</style>
