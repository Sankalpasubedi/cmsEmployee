<template>
    <div v-if="show" class="modal-overlay" @click.self="close">
      <div class="modal-content">
        <button class="modal-close"  @click="close">&times;</button>
        <slot name="title"></slot>
        <slot></slot>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: ['show'],
    methods: {
      close() {
        this.$emit('close');
      }
    }
  }
  </script>
  
  <style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  position: relative;
  max-width: 40%;
  max-height: 80%;
  overflow-y: auto;
  width: 100%;
  box-sizing: border-box;
}

.modal-close {
  position: absolute;
  top: 10px;
  right: 10px;
  background: transparent;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
}

@media (max-width: 765px) {
  .modal-content {
    max-width: 95%;
    padding: 15px;
  }

  .modal-close {
    font-size: 1.2rem;
    top: 5px;
    right: 5px;
  }
}
</style>
