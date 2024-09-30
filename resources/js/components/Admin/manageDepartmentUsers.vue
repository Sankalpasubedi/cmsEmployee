<template>
  <div class="col-12">
    <div class="bg-light rounded h-100 p-4">
      <div class="heading_container mb-4">
        <h2>Users</h2>
        <!-- Button to open the modal -->
        <button class="btn btn-primary align-items-center" style="border-radius: 50px;" @click="openCreateModal">
          <i class="fa-solid fa-plus"></i>
        </button>
      </div>
      <div class="table-responsive" style="overflow-x: scroll;">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Number</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users.data" :key="user.id">
              <th scope="row">{{ (users.current_page - 1) * users.per_page + index + 1 }}</th>
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.phone }}</td>
              <td>
                <button @click="requestSuperAdmin(user.id)" class="mydelbutton m-2">
                  Request For Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination controls -->
      <div v-if="users.last_page > 1" class="pagination-controls">
        <button @click="fetchUsers(users.current_page - 1)" :disabled="users.current_page === 1" class="pagination-button">
          Previous
        </button>

        <button v-if="showPageButton(1)" @click="fetchUsers(1)" :class="{ 'active': 1 === users.current_page }" class="pagination-button">
          1
        </button>

        <span v-if="shouldShowLeftEllipsis" class="pagination-ellipsis">...</span>

        <button v-for="page in pages" :key="page" @click="fetchUsers(page)" :class="{ 'active': page === users.current_page }" class="pagination-button">
          {{ page }}
        </button>

        <span v-if="shouldShowRightEllipsis" class="pagination-ellipsis">...</span>

        <button v-if="showPageButton(users.last_page)" @click="fetchUsers(users.last_page)" :class="{ 'active': users.last_page === users.current_page }" class="pagination-button">
          {{ users.last_page }}
        </button>

        <button @click="fetchUsers(users.current_page + 1)" :disabled="users.current_page === users.last_page" class="pagination-button">
          Next
        </button>
      </div>
    </div>

    <!-- Modal for creating a new profile -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createUserModalLabel">
              Create New Profile
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <div class="form-group">
                <label for="name">Name:</label>
                <input v-model="formData.name" type="text" id="name" class="form-control" required />
              </div>
              <div class="form-group">
                <label for="email">Email:</label>
                <input v-model="formData.email" type="email" id="email" class="form-control" required />
              </div>
              <div class="form-group">
                <label for="phone">Phone:</label>
                <input v-model="formData.phone" type="text" id="phone" class="form-control" required />
              </div>
              <div class="form-group">
                <label for="password">Password:</label>
                <input v-model="formData.password" type="password" id="password" class="form-control" required />
              </div>
              <div v-if="isSuperadmin" class="form-group">
                <label for="role">Role:</label>
                <select v-model="formData.role_id" id="role" class="form-control" required>
                  <option value="" disabled>Select</option>
                  <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.name }}
                  </option>
                </select>
              </div>
              <div v-if="isSuperadmin" class="form-group">
                <label for="department">Department:</label>
                <select v-model="formData.department_id" id="department" class="form-control" required>
                  <option value="" disabled>Select</option>
                  <option v-for="department in departments" :key="department.id" :value="department.id">
                    {{ department.name }}
                  </option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary mt-3">
                Create Profile
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

export default {
  name: 'ManageUsers',
  setup() {
    const users = ref({ data: [], current_page: 1, last_page: 1, per_page: 10 });
    const roles = ref([]);
    const departments = ref([]);
    const formData = ref({
      name: '',
      email: '',
      phone: '',
      password: '',
      role_id: '',
      department_id: ''
    });
    const showCreateModal = ref(false);
    const userRole = ref(null);
    const userDepartmentId = ref(null);

    const isSuperadmin = computed(() => userRole.value === 1);
    const isAdmin = computed(() => userRole.value === 2);

    const fetchUsers = async (page = 1) => {
      try {
        const response = await axios.get('/api/getDepartUsers', { params: { page } });
        users.value = response.data;
      } catch (error) {
        console.error('Error fetching users:', error);
      }
    };

    const fetchInitialData = async () => {
      try {
        const [rolesResponse, departmentsResponse, userResponse] = await Promise.all([
          axios.get('/api/getAllRoles'),
          axios.get('/api/departments'),
          axios.get('/api/getUser')
        ]);
        roles.value = rolesResponse.data;
        departments.value = departmentsResponse.data;
        userRole.value = userResponse.data.role_id;
        userDepartmentId.value = userResponse.data.department_id;

        if (isAdmin.value) {
          formData.value.department_id = userDepartmentId.value;
        }
      } catch (error) {
        console.error('Error fetching initial data:', error);
      }
    };

    const submitForm = async () => {
      if (isAdmin.value) {
        formData.value.role_id = 3;
        formData.value.department_id = userDepartmentId.value;
      }

      try {
        await axios.post('/api/createUser', formData.value);
        alert('Profile created successfully');
        resetForm();
        closeCreateModal();
        fetchUsers(users.value.current_page); // Fetch users for current page
      } catch (error) {
        console.error('Error creating profile:', error);
        alert('Failed to create profile');
      }
    };

    const resetForm = () => {
      formData.value = {
        name: '',
        email: '',
        phone: '',
        password: '',
        role_id: isAdmin.value ? 3 : '',
        department_id: isAdmin.value ? userDepartmentId.value : ''
      };
    };

    const openCreateModal = () => {
      const modal = new window.bootstrap.Modal(document.getElementById('createUserModal'));
      modal.show();
    };

    const closeCreateModal = () => {
      const modal = window.bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
      if (modal) {
        modal.hide();
      }
    };

    const requestSuperAdmin = async (userId) => {
      try {
        await axios.post(`/api/requestSuperAdmin/${userId}`);
        alert('Super Admin notified successfully');
      } catch (error) {
        console.error('Error notifying Super Admin:', error);
        alert('Error notifying Super Admin');
      }
    };

    const pages = computed(() => {
      const currentPage = users.value.current_page;
      const lastPage = users.value.last_page;
      let start = Math.max(currentPage - 2, 2);
      let end = Math.min(currentPage + 2, lastPage - 1);

      if (currentPage <= 3) {
        start = 2;
        end = Math.min(5, lastPage - 1);
      }

      if (currentPage >= lastPage - 2) {
        start = Math.max(lastPage - 4, 2);
        end = lastPage - 1;
      }

      const pageNumbers = [];
      for (let i = start; i <= end; i++) {
        pageNumbers.push(i);
      }
      return pageNumbers;
    });

    const shouldShowLeftEllipsis = computed(() => {
      return users.value.current_page > 4;
    });

    const shouldShowRightEllipsis = computed(() => {
      return users.value.current_page < users.value.last_page - 3;
    });

    const showPageButton = (page) => {
      return page === 1 || page === users.value.last_page || (page >= users.value.current_page - 2 && page <= users.value.current_page + 2);
    };

    onMounted(() => {
      fetchUsers();
      fetchInitialData();
    });

    return {
      users,
      roles,
      departments,
      formData,
      showCreateModal,
      isSuperadmin,
      isAdmin,
      openCreateModal,
      closeCreateModal,
      submitForm,
      resetForm,
      requestSuperAdmin,
      fetchUsers,
      pages,
      shouldShowLeftEllipsis,
      shouldShowRightEllipsis,
      showPageButton
    };
  }
};
</script>
