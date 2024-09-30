<template>
  <div class="sidebar pb-3">
    <nav class="navbar bg-light navbar-light">
      <a href="index.html" class="navbar-brand mx-4 mb-3 pe-4" style="width: 100%;">
        <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>TFT</h3>
      </a>
      <div v-if="user" class="d-flex align-items-center ms-4 mb-4">
        <div class="position-relative">
          <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
          </div>
        </div>
        <div class="ms-3">
          <h6 class="mb-0">{{ user.name }}</h6>
          <span>{{ user.role.name }}</span>
        </div>
      </div>
      <div class="navbar-nav">
        <router-link to="/dashboard" class="nav-item nav-link nav-component active">
          <i class="fa fa-tachometer-alt me-2"></i>Dashboard
        </router-link>
        <router-link to="/view-posts" class="nav-item nav-link nav-component">
          <i class="far fa-file-alt me-2"></i>View Posts
        </router-link>
        <router-link v-if="isSuperadmin" to="/manage-all-posts" class="nav-item nav-link nav-component">
          <i class="fa fa-table me-2"></i>Manage All Posts
        </router-link>
        <router-link v-if="isAdmin" to="/manage-department-posts" class="nav-item nav-link nav-component">
          <i class="fa fa-th me-2"></i>Request Posts
        </router-link>
        <router-link v-if="isSuperadmin" to="/manage-all-users" class="nav-item nav-link nav-component">
          <i class="fa fa-users me-2"></i>Manage All Users
        </router-link>
        <router-link v-if="isAdmin" to="/manage-department-users" class="nav-item nav-link nav-component">
          <i class="fa fa-users me-2"></i>Manage Users
        </router-link>
        <router-link v-if="isSuperadmin" to="/department" class="nav-item nav-link nav-component">
          <i class="fa-regular fa-building me-2"></i>Manage Department
        </router-link>
        <router-link to="/manage-user-post" class="nav-item nav-link nav-component">
          <i class="fa fa-file me-2"></i>Personal Posts
        </router-link>
      </div>
    </nav>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';

export default {
  name: "Sidebar",
  setup() {
    const store = useStore();
    const router = useRouter();
    const user = ref(null);
    const isSuperadmin = ref(false);
    const isAdmin = ref(false);
    const isUser = ref(false);

    const fetchUserData = async () => {
      try {
        const response = await axios.get('/api/getUser');
        user.value = response.data;

        if (user.value.role_id === 1) {
          isSuperadmin.value = true;
        } else if (user.value.role_id === 2) {
          isAdmin.value = true;
        } else if (user.value.role_id === 3) {
          isUser.value = true;
        }
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    };

    onMounted(() => {
      fetchUserData();
      $(".nav-item").removeClass('active');
      $(document).on("click", ".nav-component", function () {
        $(".nav-item").removeClass('active');
        $(this).addClass('active');
        if(window.screen.width < 1024 ){
          $('.sidebar-toggler').click();
        }        
      });
    });

    const logout = () => {
      store.dispatch('logout');
      router.push('/');
    };

    return {
      user,
      isSuperadmin,
      isAdmin,
      isUser,
      logout
    };
  }
};
</script>
