<template>
  <div class="col-12">
    <div class="bg-light rounded h-100 p-4">
      <h2 class="mb-4">All Posts</h2>
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
              <td>{{ post.user?.name || 'Unknown' }}</td>
              <td>
                <select v-model="post.status" @change="updateStatus(post.id, post.status)">
                  <option value="0">Inactive</option>
                  <option value="1">Private</option>
                  <option value="2">Visible</option>
                  <option value="3">Public</option>
                </select>
              </td>
              <td>
                <button @click="confirmDelete(post.id)" class="mydelbutton">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination Controls -->
      <div class="pagination-controls" v-if="posts.last_page > 1">
        <button
          @click="fetchPosts(posts.current_page - 1)"
          :disabled="posts.current_page === 1"
          class="pagination-button"
        >
          Previous
        </button>

        <button v-if="showPageButton(1)" @click="fetchPosts(1)" :class="{ 'active': 1 === posts.current_page }" class="pagination-button">
          1
        </button>

        <span v-if="shouldShowLeftEllipsis" class="pagination-ellipsis">...</span>

        <button
          v-for="page in pages"
          :key="page"
          @click="fetchPosts(page)"
          :class="{ active: page === posts.current_page }"
          class="pagination-button"
        >
          {{ page }}
        </button>

        <span v-if="shouldShowRightEllipsis" class="pagination-ellipsis">...</span>

        <button v-if="showPageButton(posts.last_page)" @click="fetchPosts(posts.last_page)" :class="{ 'active': posts.last_page === posts.current_page }" class="pagination-button">
          {{ posts.last_page }}
        </button>

        <button
          @click="fetchPosts(posts.current_page + 1)"
          :disabled="posts.current_page === posts.last_page"
          class="pagination-button"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';

export default {
  data() {
    return {
      posts: {
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 10
      }
    };
  },
  created() {
    this.fetchPosts();
  },
  methods: {
    async fetchPosts(page = 1)   {
      try {
        const response = await axios.get('/api/getPosts', { params: { page } });
        this.posts = response.data;
      } catch (error) {
        console.error('Error fetching posts:', error);
      }
    },
    async updateStatus(postId, status) {
      try {
        await axios.put(`/api/posts/${postId}`, { status });
        this.notifyStatusChange(postId, status);
        alert('Status updated successfully');
      } catch (error) {
        console.error('Error updating status:', error);
        alert('Failed to update status');
      }
    },
    async notifyStatusChange(postId, status) {
      try {
        await axios.post(`/api/posts/${postId}/notifyStatusChange`, { status });
      } catch (error) {
        console.error('Error sending notification:', error);
      }
    },
    confirmDelete(postId) {
      if (confirm('Are you sure you want to delete this post?')) {
        this.deletePost(postId);
      }
    },
    async deletePost(postId) {
      try {
        await axios.delete(`/api/posts/${postId}`);
        alert('Post deleted successfully');
        this.fetchPosts();
      } catch (error) {
        console.error('Error deleting post:', error);
        alert('Failed to delete post');
      }
    },
    showPageButton(page) {
      return page === 1 || page === this.posts.last_page || (page >= this.posts.current_page - 2 && page <= this.posts.current_page + 2);
    }
  },
  computed: {
    pages() {
      const currentPage = this.posts.current_page;
      const lastPage = this.posts.last_page;
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
    },
    shouldShowLeftEllipsis() {
      return this.posts.current_page > 4;
    },
    shouldShowRightEllipsis() {
      return this.posts.current_page < this.posts.last_page - 3;
    }
  },
};
</script>

<style scoped></style>
