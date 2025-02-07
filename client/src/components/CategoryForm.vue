<template>
  <div class="category-form card">
    <h2 class="card-title">Add New Category</h2>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="name" class="form-label">Category Name</label>
        <input id="name" v-model="name" type="text" class="form-input" required />
      </div>
      <div class="form-group">
        <label for="parent" class="form-label">Parent Category (optional)</label>
        <select id="parent" v-model="parentId" class="form-select">
          <option value="">None</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Add Category</button>
    </form>
    <p v-if="message" class="message">{{ message }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed , onMounted } from 'vue';
import { useDataStore } from '../stores/dataStore';
import type { Category } from '@/types/category';
import { useCategoryStore } from '@/stores/category';

const categoryStore = useCategoryStore();

const name = ref<string>('');
const parentId = ref<string>('');
const message = ref<string>('');

const categories = computed<Category[]>(() => categoryStore.categories);

async function handleSubmit(): Promise<void> {
  const newCategory = {
    name: name.value,
    parent_id: parentId.value  || undefined,
  };
  await categoryStore.addCategory(newCategory)
  name.value = '';
  parentId.value = '';
}
onMounted(async () => {
  await categoryStore.fetchCategories();
});
</script>

<style scoped>
.card {
  max-width: 600px;
  margin: 0 auto;
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.card-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1.5rem;
  font-family: 'Poppins', sans-serif;
}

.form-group {
  margin-bottom: 1.5rem;
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 0.875rem;
  color: #374151;
  margin-bottom: 0.5rem;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
}

.form-input,
.form-select {
  padding: 0.75rem 1rem;
  border: 1px solid #E5E7EB;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-family: 'Poppins', sans-serif;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
}

.btn-primary {
  background-color: #007bff;
  color: #fff;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.message {
  margin-top: 1rem;
  color: green;
  font-family: 'Poppins', sans-serif;
}
</style>
