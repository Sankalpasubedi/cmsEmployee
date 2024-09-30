<template id="login-form">
  <div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="login-wrapper d-flex align-items-center">
      <div class="login-left">
        <img height="100" :src="asset('images/company_logo.png')" alt="Company Logo">
      </div>
      <form class="login-right" @submit.prevent="submitForm">
        <div class="h2 text-center">LOGIN</div>
        <div class="form-group">
          <input type="text" id="email" placeholder="Email" v-model="formData.email" required>
          <label for="email">Email</label>    
        </div>
        <div class="form-group">
          <input type="password" id="Password" placeholder="Password" v-model="formData.password" required>
          <label for="Password">Password</label>    
        </div>
        <div class="button-area d-flex justify-content-center">
          <button class="btn btn-primary align-items-center">Login</button>
        </div>
        <div class="button-area d-flex justify-content-center mt-3">
          <button class="btn-secondary border-0 bg-white" @click.prevent="resetPassword">Reset Password</button>
        </div>
        <p class="text-center">Don't have an account? <router-link to="/register">Register</router-link></p>
      </form>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { useToast } from 'vue-toastification';
import { ref } from 'vue';

export default {
  name: 'Form',
  mounted() { 
    let init = 1000;
    setTimeout(() => {
      const loginWrapper = document.querySelector(".login-wrapper");
      if (loginWrapper) {
        loginWrapper.classList.toggle("open");
      }
      init = 300;
    }, init);
  },
  setup() {
    const router = useRouter();
    const store = useStore();
    const toast = useToast();
    
    const formData = ref({
      email: "",
      password: "",
    });
    const errorMessage = ref("");

    const submitForm = async () => {
      try {
        const response = await store.dispatch('login', formData.value);
        
        if (response.status === 200 || response.status === 201) {
          await router.push('/dashboard'); 
        }
      } catch (error) {
        console.error('Error in submitForm:', error);
        const errorMessageText = error.response?.data?.error || 'Login failed. Please check your credentials.';

        if (error.response?.status === 403 && errorMessageText === 'Not verified by admin') {
          toast.error('Your account has not been verified by an admin. Please contact support.');
        } else {
          toast.error(errorMessageText);
        }
        errorMessage.value = errorMessageText; 
      }
    };

    const resetPassword = async () => {
  console.log("Reset Password clicked");
  if (!formData.value.email) {
    toast.error('Please fill in the email field to reset your password.');
    return;
  }

  try {
    const response = await axios.post(
      "/api/password/reset-request",
      { email: formData.value.email }
    );
    toast.success(response.data.message);
  } catch (error) {
    toast.error(error.response?.data?.message || 'Error sending reset link.');
  }
};

    return { router, store, toast, formData, errorMessage, submitForm, resetPassword };
  },
};
</script>




<style>
* {
  box-sizing: border-box;
}

html,
body {
  margin: 0;
  padding: 0;
  height: 100%;
}

#sandbox {
  font-family: "Lato", sans-serif;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  flex-direction: row-reverse;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  background: linear-gradient(243.87deg, #22bf64 30.6%, #371933 130.6%);
  overflow: hidden;
}

input {
  font-family: inherit;
  border: 0;
  font-size: 16px;
  color: #000;
  border-radius: 0;
}

input[type="text"],
input[type="password"] {
  width: 100%;
  height: 40px;
  border-bottom: 1px solid #aaaaaa;
}

button,
input:focus {
  outline: 0;
}

::-webkit-input-placeholder {
  font-size: 16px;
  font-weight: 300;
  letter-spacing: -0.00933333em;
}

.form-group {
  position: relative;
  padding-top: 15px;
  margin-top: 10px;
}

label {
  position: absolute;
  top: -8px;
  left: 0;
  opacity: 1;
  -webkit-transform: translateY(5px);
  transform: translateY(5px);
  color: #aaa;
  font-weight: 300;
  font-size: 13px;
  letter-spacing: -0.00933333em;
  transition: all 0.2s ease-out;
}

input:placeholder-shown + label {
  opacity: 0;
  -webkit-transform: translateY(15px);
  transform: translateY(15px);
}

.h1 {
  color: #fff;
  opacity: 0.8;
  font-size: 20px;
  font-weight: 400;
  text-transform: uppercase;
  letter-spacing: 0.2405em;
  transition: all 770ms cubic-bezier(0.51, 0.04, 0.12, 0.99);
  text-align: center;
  cursor: pointer;
  position: absolute;
  transform: translateY(-10px);
}

.open .h1 {
  -webkit-transform: translateX(200px) translateZ(0) translateY(-10px);
  transform: translateX(200px) translateZ(0) translateY(-10px);
}

.h2 {
  color: #000;
  font-size: 20px;
  letter-spacing: -0.00933333em;
  font-weight: 600;
  padding-bottom: 15px;
}

.login-wrapper {
  width: 800px;
  height: 440px;
  background-color: #fff;
  box-shadow: 0px 2px 50px rgba(0, 0, 0, 0.2);
  border-radius: 4px;
  overflow: hidden;
  position: relative;
}

.login-left {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: all 770ms cubic-bezier(0.51, 0.04, 0.12, 0.99);
  overflow: hidden;
}

.login-left img {
  display: block;
  transition: all 770ms cubic-bezier(0.51, 0.04, 0.12, 0.99);
  object-position: left;
}

.open .login-left img {
  -webkit-transform: translateX(210px) translateZ(0);
  transform: translateX(210px) translateZ(0);
}

.open .login-left {
  -webkit-transform: translateX(-400px) translateZ(0);
  transform: translateX(-400px) translateZ(0);
}

.login-right {
  padding: 40px;
  position: absolute;
  top: 0;
  right: 0;
  width: 400px;
  -webkit-transform: translateX(400px) translateZ(0);
  transform: translateX(400px) translateZ(0);
  transition: all 770ms cubic-bezier(0.51, 0.04, 0.12, 0.99);
}

.open .login-right {
  -webkit-transform: translateX(0px) translateZ(0);
  transform: translateX(0px) translateZ(0);
}

.checkbox-container {
  display: flex;
  margin-top: 35px;
}

.text-checkbox {
  color: #aaa;
  font-size: 16px;
  letter-spacing: -0.00933333em;
  font-weight: 300;
  margin-left: 15px;
}

input[type="checkbox"] {
  cursor: pointer;
  margin: 0;
  height: 22px;
  position: relative;
  width: 22px;
  transition: all 180ms linear;
}

input[type="checkbox"]:before {
  border: 1px solid #aaa;
  background-color: #fff;
  content: "";
  width: 20px;
  height: 20px;
  display: block;
  border-radius: 2px;
  transition: all 180ms linear;
}

input[type="checkbox"]:checked:before {
  background: linear-gradient(198.08deg, #22bf64 45.34%, #e281e7 224.21%);
  border: 1px solid #22bf64;
}

input[type="checkbox"]:after {
  content: "";
  border: 2px solid #fff;
  border-right: 0;
  border-top: 0;
  display: block;
  height: 4px;
  left: 4px;
  opacity: 0;
  position: absolute;
  top: 6px;
  -webkit-transform: rotate(-45deg);
  transform: rotate(-45deg);
  width: 12px;
  transition: all 180ms linear;
}

input[type="checkbox"]:checked:after {
  opacity: 1;
}

.button-area {
  display: flex;
  justify-content: space-between;
  margin-top: 30px;
}

.btn {
  font-family: inherit;
  background-color: transparent;
  border: none;
  border-radius: 2px;
  height: 40px;
  display: flex;
  padding: 0 35px;
  cursor: pointer;
  font-size: 16px;
  text-transform: uppercase;
  letter-spacing: -0.00933333em;
}

.btn-primary {
  color: #fff;
  background: linear-gradient(198.08deg, #22bf64 45.34%, #e281e7 224.21%);
  box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
  text-align: right;
}

.btn-primary:hover,
.btn-primary:focus {
  color: #fff;
  background: linear-gradient(198.08deg, #1b984f 45.34%, #e281e7 224.21%);
}

.btn-secondary {
  color: #22bf64;
}
.btn-secondary:focus,
.btn-secondary:hover {
  color: #1b984f;
}
</style>