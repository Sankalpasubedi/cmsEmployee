<template>
  <div class="container">
    <div class="heading_container">
      <h1>Posts</h1>
      <!-- Button to open the modal -->
      <button class="btn btn-primary align-items-center" style="border-radius: 50px;" @click="openCreateModal">
        <i class="fa-solid fa-plus"></i>
      </button>
    </div>

    <!-- LOADING -->
    <div v-if="loading">Loading posts...</div>

    <!-- POSTS -->
    <div v-if="posts.data && posts.data.length" class="row">
      <div v-for="post in posts.data" :key="post.id" class="col-lg-4 col-md-6 col-12 post p-2">
        <!-- POSTCARD -->
        <div class="postcard">
          <div class="imgBg">
            <img v-if="isImage(post.file)" :src="asset('/storage/' + post.file)" alt="Post Image" width="100%" style="max-height: 100%;">
            <iframe v-if="isDocument(post.file)" :src="asset('/storage/' + post.file)" frameborder="0" height="100%" width="100%" style="max-height: 100%;"></iframe>
            <div v-if="isOfficeFile(post.file)" class="text-preview text-center " width="100%">
              <p style="color: red;">Text file preview (not supported in this view).</p>
            </div>
            <div class="w-100 d-flex justify-content-end downloadBtnContainer">
              <a :href="asset('/storage/' + post.file)" download>
                <button class="downloadButton"><i class="fa fa-download"></i></button>
              </a>
            </div>
          </div>
          <div class="userDeatils">
            <div class="profileImg"></div>
            <h4 class="title">
              <span style="font-weight: 500;">{{ post.title }}</span>
            </h4>
          </div>
          <div style="font-size: 0.8em;"><i class="fa fa-user"></i>&nbsp;{{ post.user.name }}</div>
          <div style="font-size: 0.8em;"><i class="fa fa-users"></i>&nbsp;{{ post.user.department.name }}</div>
          <div class="post_description">
            <p>{{ post.description }}</p>
          </div>
        </div>
        <!-- POSTCARD END -->
      </div>
    </div>
    <div v-else>
      <p>No posts available.</p>
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



    <!-- Modal for creating post -->
    <div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createPostModalLabel">Create Post</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <div class="form-group">
                <label for="title">Title:</label>
                <input v-model="formData.title" type="text" id="title" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="description">Description:</label>
                <textarea v-model="formData.description" id="description" class="form-control" required></textarea>
              </div>
              <div class="form-group">
                <input type="file" @change="handleFileUpload" id="file" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary mt-3">Create Post</button>
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
  name: "ViewPosts",
  setup() {
    const posts = ref({});
    const loading = ref(true);
    const showCreateModal = ref(false);
    const formData = ref({
      title: '',
      description: '',
      file: null,
      status: 0
    });

    // Fetch posts
    const fetchPosts = async (page = 1) => {
      loading.value = true;
      try {
        const response = await axios.get('/api/getPosts',{
          params : {page}
        });
        posts.value = response.data;
      } catch (error) {
        console.error('Error fetching posts:', error);
      } finally {
        loading.value = false;
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
    // Check file type
    const getFileExtension = (fileName) => {
      return fileName.split('.').pop().toLowerCase();
    };

    const isImage = (file) => ['jpg', 'jpeg', 'ico','png','svg', 'gif', 'bmp', 'webp', 'tiff'].includes(getFileExtension(file));
    const isDocument = (file) => ['pdf','txt','html','css', 'xml', 'json', 'yaml'].includes(getFileExtension(file));
    const isDoc = (file) => ['doc', 'docx'].includes(getFileExtension(file));
    const isPpt = (file) => ['ppt', 'pptx'].includes(getFileExtension(file));
    const isOfficeFile = (file) => isDoc(file) || isPpt(file);

    // Handle file upload
    const handleFileUpload = (event) => {
      formData.value.file = event.target.files[0];
    };

    // Open modal
    const openCreateModal = () => {
      const modal = new window.bootstrap.Modal(document.getElementById('createPostModal'));
      modal.show();

     const modalElement = document.getElementById('createPostModal');
      modalElement.addEventListener('hidden.bs.modal', () => {
        resetForm();
      });
    };

    // Submit form
    const submitForm = async () => {
      const form = new FormData();
      form.append('title', formData.value.title);
      form.append('description', formData.value.description);
      form.append('file', formData.value.file);
      form.append('status', formData.value.status);

      try {
        await axios.post('/api/posts', form, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        alert('Post created');
        resetForm();
        closeCreateModal();
        fetchPosts();
      } catch (error) {
        console.error('Error creating post:', error);
      }
    };

    // Reset form
    const resetForm = () => {
      formData.value.title = '';
      formData.value.description = '';
      formData.value.file = null;
      formData.value.status = 0;
    };

    // Close modal
    const closeCreateModal = () => {
      const modal = window.bootstrap.Modal.getInstance(document.getElementById('createPostModal'));
      if (modal) {
        modal.hide();
      }
      resetForm();
    };

    onMounted(() => {
      fetchPosts();
    });

    return {
      posts,
      loading,
      showCreateModal,
      formData,
      isImage,
      fetchPosts,
      isDocument,
      isOfficeFile,
      handleFileUpload,
      submitForm,
      resetForm,
      openCreateModal,
      closeCreateModal,
      pages,
      shouldShowLeftEllipsis,
      shouldShowRightEllipsis,
      showPageButton
    };
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

.mt-3 {
  margin-top: 1rem;
}
</style>
