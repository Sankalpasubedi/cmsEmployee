<template>
  <div>
    <h2>Manage Departments</h2>
    <div class="row">
      <div class="col-lg-6 col-sm-12 col-xl-6">
        <div class="bg-light rounded h-100 p-3">
          <h6 class="mb-4">Departments</h6>
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Edit</th>
                <th scope="col">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(department, index) in paginatedDepartments" :key="department.id">
                <th scope="row">{{ (currentPage - 1) * perPage + index + 1 }}</th>
                <td>{{ department.name }}</td>
                <td>
                  <button @click="editDepartment(department)" class="myeditbutton">Edit</button>
                </td>
                <td>
                  <button @click="deleteDepartment(department.id)" class="mydelbutton">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        
            <!-- Pagination controls -->
    <div class="pagination-controls">
      <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="pagination-button">
        Previous
      </button>

      <button v-for="page in totalPages" :key="page" @click="changePage(page)" :class="{ 'active': page === currentPage }" class="pagination-button">
        {{ page }}
      </button>

      <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages" class="pagination-button">
        Next
      </button>
    </div>
      </div>
    </div>
      <div class="col-lg-6 col-sm-12 col-xl-6">
        <div class="bg-light rounded p-4">
          <h6 class="mb-4">Create Department</h6>
          <form @submit.prevent="createDepartment">
            <div class="mb-3">
              <input type="text" v-model="newDepartment.name" id="name" required placeholder="Name"/>
            </div>
            <button type="submit" class="btn btn-primary align-items-center">Create</button>
          </form>
        </div>
      </div>
    </div>

  <!-- Modal for editing department -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="updateDepartment">
          <div class="form-group mb-3">
            <label for="editDepartmentName">Name:</label>
            <input type="text" v-model="selectedDepartment.name" id="editDepartmentName" class="form-control" required />
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>




  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const departments = ref({
      data: [],
      current_page: 1,
      last_page: 1,
      per_page: 5,
      total: 0
    });
    const newDepartment = ref({ name: '' });
    const selectedDepartment = ref({
  name: ''
});
    const currentPage = ref(1);
    const perPage = ref(5);

    const fetchDepartments = async (page = 1) => {
      try {
        const response = await axios.get('/api/departments', { params: { page, per_page: perPage.value } });
        departments.value = response.data;
        currentPage.value = response.data.current_page;
      } catch (error) {
        console.error('Error fetching departments:', error);
      }
    };

    const createDepartment = async () => {
  try {
    await axios.post('/api/departments', newDepartment.value);
    
    newDepartment.value.name = '';
    
    
    await fetchDepartments(currentPage.value);
    
    console.log('Departments updated successfully.');
  } catch (error) {
    console.error('Error creating department:', error);
  }
};


    const editDepartment = (department) => {
      selectedDepartment.value = { ...department };
    };

    const updateDepartment = async () => {
      try {
        await axios.put(`/api/departments/${selectedDepartment.value.id}`, selectedDepartment.value);
        await fetchDepartments(currentPage.value);
        selectedDepartment.value = null;
        closeEditModal();
      } catch (error) {
        console.error('Error updating department:', error);
      }
    };

    const deleteDepartment = async (departmentId) => {
      try {
        await axios.delete(`/api/departments/${departmentId}`);
        await fetchDepartments(currentPage.value);
      } catch (error) {
        console.error('Error deleting department:', error);
      }
    };

    const openEditModal = () => {
      try {
        const modalElement = document.getElementById('editDepartmentModal');
        if (modalElement) {
          const modal = new window.bootstrap.Modal(modalElement);
          modal.show();
        } else {
          console.error('Modal element not found');
        }
      } catch (error) {
        console.error('Error opening modal:', error);
      }
    };

    const closeEditModal = () => {
      try {
        const modalElement = document.getElementById('editDepartmentModal');
        if (modalElement) {
          const modal = window.bootstrap.Modal.getInstance(modalElement);
          if (modal) {
            selectedDepartment.value = null;
            modal.hide();
          } else {
            console.error('Modal instance not found');
          }
        } else {
          console.error('Modal element not found');
        }
      } catch (error) {
        console.error('Error closing modal:', error);
      }
    };

    watch(selectedDepartment, (newVal) => {
      if (newVal) {
        openEditModal();
      }
    });

    const changePage = (page) => {
      if (page < 1 || page > departments.value.last_page) return;
      currentPage.value = page;
      fetchDepartments(page);
    };

    const paginatedDepartments = computed(() => departments.value.data);
    const totalPages = computed(() => departments.value.last_page);

    onMounted(() => {
      fetchDepartments();
    });

    return {
      departments,
      newDepartment,
      selectedDepartment,
      currentPage,
      perPage,
      totalPages,
      paginatedDepartments,
      fetchDepartments,
      createDepartment,
      editDepartment,
      updateDepartment,
      deleteDepartment,
      closeEditModal,
      changePage
    };
  }
};
</script>