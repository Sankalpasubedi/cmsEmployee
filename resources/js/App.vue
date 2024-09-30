<template>
    <div>

           <!-- Spinner Start -->
           <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->
         
      <div v-if="isAuthenticated">
        <div class="container-xxl position-relative bg-white d-flex p-0">
          <Sidebar />
          <div class="content">
            <Navbar />
            <div class="p-3">
              <router-view></router-view>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <div>
          <router-view></router-view>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { computed, onMounted } from 'vue';
  import { useStore } from 'vuex';
  import Sidebar from './layout/sidebar.vue';
  import Navbar from './layout/navbar.vue';

  export default {
    name: 'App',
    components: {
      Sidebar,
      Navbar
    },
    setup() {
      const store = useStore();
  
      const isAuthenticated = computed(() => {
        return !!store.state.token;
      });
  
      return {
        isAuthenticated,
      };
    },
  };
  </script>
  
  <style scoped>
  .app {
    display: flex;
  }
  
  .main-content {
    flex: 1;
    padding: 20px;
  }
  </style>
  