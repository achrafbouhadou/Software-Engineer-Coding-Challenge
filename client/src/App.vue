<!-- src/App.vue -->
<template>
  <div id="app">
    <h1>Product Management</h1>
    <nav class="tabs">
      <button :class="{ active: activeTab === 'list' }" @click="activeTab = 'list'">
        Product List
      </button>
      <button :class="{ active: activeTab === 'category' }" @click="activeTab = 'category'">
        Add Category
      </button>
      <button :class="{ active: activeTab === 'product' }" @click="activeTab = 'product'">
        Add Product
      </button>
    </nav>
    <section class="tab-content">
      <ProductList v-if="activeTab === 'list'" />
      <CategoryForm v-if="activeTab === 'category'" />
      <ProductForm v-if="activeTab === 'product'" />
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import ProductList from './components/ProductList.vue';
import CategoryForm from './components/CategoryForm.vue';
import ProductForm from './components/ProductForm.vue';

const activeTab = ref<'list' | 'category' | 'product'>('list');
</script>

<style>
#app {
  font-family: Arial, sans-serif;
  padding: 20px;
}
.tabs {
  margin-bottom: 20px;
}
.tabs button {
  padding: 10px 15px;
  margin-right: 10px;
  border: none;
  background-color: #eee;
  cursor: pointer;
  border-radius: 4px;
}
.tabs button.active {
  background-color: #007bff;
  color: #fff;
}
.tab-content {
  margin-top: 20px;
}
</style>
