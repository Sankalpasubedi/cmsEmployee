 <template>
  <div class="col-12">
    <div class="bg-light rounded h-100 p-4">
      <h2 class="mb-4">Personal Posts</h2>
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
                <div class="d-flex align-items-center">
                  <button @click="editPost(post)" class="myeditbutton m-1">Edit</button>
                  <button v-if="post.user.role_id == 1 || post.user.role_id == 2" @click="deletePost(post.id)"
                    class="mydelbutton m-1">Delete</button>
                  <button v-else @click="deletePostRequest(post.id)" class="mydelbutton m-1">Request Delete</button>
                </div>
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
    
   <!-- Modal for editing post -->
   <Modal :show="showModal" @close="closeModal">
    <template v-slot:title>Edit Post</template>
    <template v-if="selectedPost">
      <form @submit.prevent="updatePost">
        <div class="form-group">
          <label for="title">Title:</label>
          <input type="text" v-model="selectedPost.title" id="title" class="form-control" required />
        </div>
        <div class="form-group">
          <label for="description">Description:</label>
          <textarea v-model="selectedPost.description" id="description" class="form-control" required></textarea>
        </div>
        <div class="form-group">
          <label for="file">File:</label>
          <input type="file" @change="onFileChange" id="file" class="form-control" />
        </div>
        <button type="submit" class="btn btn-primary mt-3">Update Post</button>
      </form>
    </template>
    <template v-else>
      <p>No post selected for editing.</p>
    </template>
  </Modal>
  

  </div>
</template>

 



<script>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import Modal from './modal.vue';
export default {
  components:{
    Modal,
  },
  setup() {
    const posts = ref({ data: [], current_page: 1, last_page: 1, per_page: 10 });
    const selectedPost = ref(null);
    const newFile = ref(null);
    const showModal = ref(false);

    const fetchPosts = async (page = 1) => {
      try {
        const response = await axios.get('/api/getPersonalPosts', { params: { page } });
        posts.value = response.data;
      } catch (error) {
        console.error('Error fetching posts:', error);
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

      return Array.from({ length: end - start + 1 }, (_, i) => i + start);
    });

    const shouldShowLeftEllipsis = computed(() => posts.value.current_page > 4);
    const shouldShowRightEllipsis = computed(() => posts.value.current_page < posts.value.last_page - 3);

    const showPageButton = (page) => {
      return page === 1 || page === posts.value.last_page;
    };

    const onFileChange = (event) => {
      newFile.value = event.target.files[0];
    };

    const editPost = (post) => {
      selectedPost.value = { ...post };
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      selectedPost.value = null;
      newFile.value = null;
    };

    const updatePost = async () => {
      const formData = new FormData();
      formData.append('title', selectedPost.value.title);
      formData.append('description', selectedPost.value.description);
      formData.append('status', selectedPost.value.status);
      if (newFile.value) {
        formData.append('file', newFile.value);
      }

      try {
        await axios.post(`/api/updatePost/${selectedPost.value.id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        fetchPosts(posts.value.current_page); // Fetch posts for current page
        closeModal();
      } catch (error) {
        console.error('Error updating post:', error);
      }
    };

    const deletePostRequest = async (postId) => {
      try {
        await axios.post(`/api/deletePostRequest/${postId}`);
        alert('Request sent successfully');
        fetchPosts(posts.value.current_page); // Fetch posts for current page
      } catch (error) {
        console.error('Error notifying:', error);
        alert('Error!');
      }
    };

    const deletePost = async (postId) => {
      try {
        await axios.delete(`/api/posts/${postId}`);
        alert('Post Deleted!');
        fetchPosts(posts.value.current_page); // Fetch posts for current page
      } catch (error) {
        console.error('Error:', error);
        alert('Error!');
      }
    };

    onMounted(() => {
      fetchPosts();
    });

    return {
      posts,
      selectedPost,
      newFile,
      showModal,
      fetchPosts,
      getStatusLabel,
      onFileChange,
      editPost,
      closeModal,
      updatePost,
      deletePostRequest,
      deletePost,
      pages,
      shouldShowLeftEllipsis,
      shouldShowRightEllipsis,
      showPageButton
    };
  }
};
</script>
 
<style scoped>
.form-group {
  margin-bottom: 15px;
}

.form-control {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
}

</style>