<template>
  <div id="app">
    <div class="header">
      <h1>Product Management</h1>
      <button class="create-button" @click="showModal = true">Create Product</button>
    </div>
    <ProductTable />
    <ProductModal v-if="showModal" @close="showModal = false" />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import ProductTable from './components/ProductTable.vue';
import ProductModal from './components/ProductModal.vue';

export default defineComponent({
  name: 'App',
  components: {
    ProductTable,
    ProductModal,
  },
  setup() {
    const showModal = ref(false);
    return { showModal };
  },
});
</script>

<style>
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 20px;
  background-color: #f5f5f5;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.create-button {
  padding: 10px 15px;
  font-size: 14px;
  cursor: pointer;
  border: none;
  background-color: #007bff;
  color: #fff;
  border-radius: 4px;
}

.create-button:hover {
  background-color: #0056b3;
}
</style>
