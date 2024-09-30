<template>
  <div class="col-12">
    <div class="bg-light rounded h-100 p-4">
      <h2 class="mb-4">Department Posts</h2>
      <div class="table-responsive" style="overflow-x: scroll;">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Title</th>
              <th scope="col">Description</th>
              <th scope="col">User</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(post, index) in posts.data" :key="post.id">
              <th scope="row">{{ (posts.current_page - 1) * posts.per_page + index + 1 }}</th>
              <td>{{ post.title }}</td>
              <td>
                <div class="post_description" style="min-height: 1px; max-height: 7rem;">{{ post.description }}</div>
              </td>
              <td>{{ post.user.name }}</td>
              <td>{{ getStatusLabel(post.status) }}</td>
              <td>
                <button @click="notifySuperAdmin(post.id)" class="myeditbutton">Notify Super Admin</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination controls -->
      <div v-if="posts.last_page > 1" class="pagination-controls">
        <button @click="fetchPosts(posts.current_page - 1)" :disabled="posts.current_page === 1" class="pagination-button">
          Previous
        </button>

        <button v-if="showPageButton(1)" @click="fetchPosts(1)" :class="{ 'active': 1 === posts.current_page }" class="pagination-button">
          1
        </button>

        <span v-if="shouldShowLeftEllipsis" class="pagination-ellipsis">...</span>

        <button v-for="page in pages" :key="page" @click="fetchPosts(page)" :class="{ 'active': page === posts.current_page }" class="pagination-button">
          {{ page }}
        </button>

        <span v-if="shouldShowRightEllipsis" class="pagination-ellipsis">...</span>

        <button v-if="showPageButton(posts.last_page)" @click="fetchPosts(posts.last_page)" :class="{ 'active': posts.last_page === posts.current_page }" class="pagination-button">
          {{ posts.last_page }}
        </button>

        <button @click="fetchPosts(posts.current_page + 1)" :disabled="posts.current_page === posts.last_page" class="pagination-button">
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { ref, computed, onMounted } from 'vue';

export default {
  setup() {
    const posts = ref({ data: [], current_page: 1, last_page: 1, per_page: 10 });
    const loading = ref(true);

    const fetchPosts = async (page = 1) => {
      try {
        const response = await axios.get('/api/getUnverifiedPosts', { params: { page, per_page: posts.value.per_page } });
        posts.value = response.data;
      } catch (error) {
        console.error('Error fetching posts:', error);
      } finally {
        loading.value = false;
      }
    };

    const getStatusLabel = (status) => {
      switch (status) {
        case 0: return 'Inactive';
        case 1: return 'Private';
        case 2: return 'Visible';
        case 3: return 'Public';
        default: return 'Unknown';
      }
    };

    const notifySuperAdmin = async (postId) => {
      try {
        await axios.post(`/api/notifySuperAdmin/${postId}`);
        alert('Super Admin notified successfully');
      } catch (error) {
        console.error('Error notifying Super Admin:', error);
        alert('Error notifying Super Admin');
      }
    };

    const pages = computed(() => {
      const currentPage = posts.value.current_page;
      const lastPage = posts.value.last_page;
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

    const shouldShowLeftEllipsis = computed(() => posts.value.current_page > 4);
    const shouldShowRightEllipsis = computed(() => posts.value.current_page < posts.value.last_page - 3);

    const showPageButton = (page) => {
      return page === 1 || page === posts.value.last_page || (page >= posts.value.current_page - 2 && page <= posts.value.current_page + 2);
    };

    onMounted(() => {
      fetchPosts();
    });

    return {
      posts,
      loading,
      fetchPosts,
      getStatusLabel,
      notifySuperAdmin,
      pages,
      shouldShowLeftEllipsis,
      shouldShowRightEllipsis,
      showPageButton
    };
  }
};
</script>

<style scoped>
li {
  border: 1px solid #ccc;
  padding: 1rem;
  margin-bottom: 1rem;
}
</style>
