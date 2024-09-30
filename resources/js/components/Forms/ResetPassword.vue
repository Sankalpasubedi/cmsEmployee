<template>
      <!-- Modal structure for password reset -->
       <div class="d-flex justify-content-center m-5">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resetPasswordModalLabel">
          Reset Your Password
        </h5>
      </div>
      <div class="modal-body">
        <form @submit.prevent="resetPassword">
          <div class="form-group">
            <label for="password">New Password:</label>
            <input
              type="password"
              id="password"
              v-model="password"
              class="form-control"
              required
            />
          </div>
          <div class="form-group">
            <label for="password_confirmation">Confirm Password:</label>
            <input
              type="password"
              id="password_confirmation"
              v-model="password_confirmation"
              class="form-control"
              required
            />
          </div>
          <button type="submit" class="btn btn-primary m-1">Reset Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

  </template>
  
  <script>
  import axios from 'axios';
  import { ref } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import { useToast } from 'vue-toastification';

  export default {
    setup() {
      const route = useRoute();
      const router = useRouter();
      const password = ref('');
      const password_confirmation = ref('');
      const errorMessage = ref(null);
      const successMessage = ref(null);
      const toast = useToast();

      const resetPassword = async () => {
        if (password.value !== password_confirmation.value) {
          toast.error('Passwords do not match.');
          return;
        }
        
        try {
          const token = route.params.token; 
          await axios.post('/api/password/reset', {
            token,
            password: password.value,
            password_confirmation: password_confirmation.value
          });
          toast.success('Password reset successfully.');

          router.push('/'); 
        } catch (error) {
          toast.error(error.response?.data?.message || 'An error occurred.');

        }
      };
      
      return {
        password,
        password_confirmation,
        errorMessage,
        successMessage,
        resetPassword
      };
    }
  };
  </script>
  
  <style scoped>
  </style>
  