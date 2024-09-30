<template>
  <div>
    <h1>Update User</h1>
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
      <div class="flex mt-3">
        <button type="submit" class="btn btn-primary">Update</button>
        <button v-if="formData.role_id != 1" @click.prevent="requestSuperAdmin" class="btn btn-warning ms-2">
          Request For Delete
        </button>
        <button @click.prevent="resetPassword" class="btn btn-secondary ms-2">Reset Password</button>
      </div>
      <div v-if="loading" class="mt-3">Loading...</div>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    close: Function // Function to close the modal
  },
  data() {
    return {
      formData: {
        id: '',
        name: '',
        email: '',
        phone: '',
        role_id: '1'
      },
      loading: false
    };
  },
  methods: {
    async fetchUserData() {
      this.loading = true;
      try {
        const response = await axios.get('/api/getUser');
        this.formData = response.data;
      } catch (error) {
        console.error('Error fetching user data:', error);
      } finally {
        this.loading = false;
      }
    },
    async updateUser() {
      this.loading = true;
      try {
        await axios.put('/api/user/' + this.formData.id, this.formData);
        alert('User updated successfully');
        this.close(); // Close modal after update
      } catch (error) {
        console.error('Error updating user:', error);
        alert('Error updating user');
      } finally {
        this.loading = false;
      }
    },
    async requestSuperAdmin() {
      try {
        await axios.post(`/api/requestSuperAdmin/${this.formData.id}`);
        alert('Super Admin notified successfully');
      } catch (error) {
        console.error('Error notifying Super Admin:', error);
        alert('Error notifying Super Admin');
      } finally {
        this.loading = false;
      }
    },
    async resetPassword() {
      this.loading = true;
      try {
        const response = await axios.post('/api/password/reset-request', { email: this.formData.email });
        alert(response.data.message);
      } catch (error) {
        console.error('Error sending reset link:', error);
        alert('Error sending reset link');
      } finally {
        this.loading = false;
      }
    }
  },
  created() {
    this.fetchUserData();
  }
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
