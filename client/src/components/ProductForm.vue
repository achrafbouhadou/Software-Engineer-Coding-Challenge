<template>
  <div class="product-form card">
    <h2 class="card-title">Add New Product</h2>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="name" class="form-label">Product Name</label>
        <input id="name" v-model="name" type="text" class="form-input" required />
      </div>
      <div class="form-group">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" v-model="description" class="form-input" required></textarea>
      </div>
      <div class="form-group">
        <label for="price" class="form-label">Price</label>
        <input id="price" v-model="price" type="number" step="0.01" class="form-input" required />
      </div>
      <div class="form-group">
        <label class="form-label">Upload Product Image</label>
        <div class="file-upload">
          <input id="image" type="file" accept="image/*" @change="handleImageUpload" class="file-input" />
          <span v-if="!image" class="file-placeholder">Choose an image file...</span>
          <div v-if="image" class="image-preview-container">
            <img :src="image" class="image-preview" alt="Image Preview" />
            <button type="button" class="btn btn-secondary remove-btn" @click="removeImage">Remove Image</button>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Select Categories</label>
        <div class="category-tree">
          <CategoryCheckbox
            v-for="cat in categoryTree"
            :key="cat.id"
            :category="cat"
            v-model:checkedCategories="selectedCategories"
          />
        </div>
      </div>
      <button type="submit" class="btn btn-success">Add Product</button>
    </form>
    <p v-if="message" class="message">{{ message }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useDataStore } from '../stores/dataStore';
import type { Product, Category } from '../stores/dataStore';
import CategoryCheckbox from './CategoryCheckbox.vue';

interface CategoryNode extends Category {
  children?: CategoryNode[];
}

const store = useDataStore();

const name = ref<string>('');
const description = ref<string>('');
const price = ref<string>('');
const image = ref<string>(''); // Holds the image data URL
const selectedCategories = ref<number[]>([]);
const message = ref<string>('');

const categories = computed<Category[]>(() => store.categories);

function buildCategoryTree(categories: Category[], parentId: number | null = null): CategoryNode[] {
  return categories
    .filter(cat => cat.parentId === parentId)
    .map(cat => ({
      ...cat,
      children: buildCategoryTree(categories, cat.id),
    }));
}

const categoryTree = computed<CategoryNode[]>(() => buildCategoryTree(categories.value));

function handleImageUpload(event: Event): void {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    const reader = new FileReader();
    reader.onload = (e) => {
      image.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
}

function removeImage(): void {
  image.value = '';
  // Optionally, you might also clear the file input value:
  const fileInput = document.getElementById('image') as HTMLInputElement;
  if (fileInput) {
    fileInput.value = '';
  }
}

function handleSubmit(): void {
  const newProduct = {
    name: name.value,
    description: description.value,
    price: parseFloat(price.value),
    image: image.value,
    categories: selectedCategories.value,
  };
  store.addProduct(newProduct);
  message.value = `Product "${name.value}" added successfully.`;
  name.value = '';
  description.value = '';
  price.value = '';
  image.value = '';
  selectedCategories.value = [];
}
</script>

<style scoped>
.card {
  max-width: 600px;
  margin: 0 auto;
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

.form-input {
  padding: 0.75rem 1rem;
  border: 1px solid #E5E7EB;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-family: 'Poppins', sans-serif;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.file-upload {
  position: relative;
  border: 1px dashed #E5E7EB;
  border-radius: 0.5rem;
  padding: 1rem;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s;
}

.file-upload:hover {
  border-color: #3B82F6;
}

.file-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.file-placeholder {
  font-size: 0.875rem;
  color: #6B7280;
  font-family: 'Poppins', sans-serif;
}

.image-preview-container {
  position: relative;
  margin-top: 1rem;
}

.image-preview {
  max-width: 100%;
  max-height: 200px;
  border-radius: 0.5rem;
}

.remove-btn {
  margin-top: 0.5rem;
  padding: 0.5rem 1rem;
  border: none;
  background-color: #dc2626;
  color: white;
  border-radius: 0.5rem;
  font-family: 'Poppins', sans-serif;
  font-size: 0.75rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.remove-btn:hover {
  background-color: #b91c1c;
}

.category-tree {
  border: 1px solid #E5E7EB;
  border-radius: 0.5rem;
  padding: 1rem;
  max-height: 200px;
  overflow-y: auto;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
}

.btn-success {
  background-color: #28a745;
  color: #fff;
  transition: background-color 0.2s;
}

.btn-success:hover {
  background-color: #218838;
}

.message {
  margin-top: 1rem;
  color: green;
  font-family: 'Poppins', sans-serif;
}
</style>
