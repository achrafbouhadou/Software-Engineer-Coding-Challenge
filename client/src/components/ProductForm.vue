<template>
  <div class="product-form card">
    <h2 class="card-title">Add New Product</h2>
    <form @submit.prevent="handleSubmit">
      <!-- Product fields -->
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

      <!-- Image Source Selection -->
      <div class="form-group">
        <label class="form-label">Image Source</label>
        <div class="radio-group">
          <label>
            <input type="radio" value="file" v-model="uploadMethod" />
            Upload File
          </label>
          <label>
            <input type="radio" value="url" v-model="uploadMethod" />
            Use Image URL
          </label>
        </div>
      </div>

      <!-- Conditional rendering for file upload or URL input -->
      <div class="form-group">
        <!-- File Upload -->
        <div v-if="uploadMethod === 'file'" class="file-upload">
          <input id="fileInput" type="file" accept="image/*" @change="handleFileUpload" class="file-input" />
          <span v-if="!imageFile" class="file-placeholder">Choose an image file...</span>
          <div v-if="imageFile" class="image-preview-container">
            <img :src="imagePreview" class="image-preview" alt="Image Preview" />
            <button type="button" class="btn btn-secondary remove-btn" @click="removeFile">
              Remove Image
            </button>
          </div>
        </div>
        <!-- URL Input -->
        <div v-if="uploadMethod === 'url'" class="url-upload">
          <input
            id="urlInput"
            type="text"
            v-model="imageUrl"
            placeholder="Enter image URL"
            class="form-input url-upload"
          />
          <div v-if="imageUrl" class="image-preview-container">
            <img :src="imageUrl" class="image-preview" alt="Image Preview" />
            <button type="button" class="btn btn-secondary remove-btn" @click="removeUrl">
              Remove Image URL
            </button>
          </div>
        </div>
      </div>

      <!-- Categories via checkboxes -->
      <div class="form-group">
        <label class="form-label">Select Categories</label>
        <div class="category-tree">
          <CategoryCheckbox
            v-for="cat in categories"
            :key="cat.id"
            :category="cat"
            v-model:checkedCategories="selectedCategories"
          />
        </div>
      </div>

      <!-- OR: Search for a Category via Modal -->
      <div class="form-group">
        <button type="button" class="btn btn-info" @click="openModal">
          Search for Category
        </button>
      </div>

      <button type="submit" class="btn btn-success">Add Product</button>
    </form>
    <p v-if="message" class="message">{{ message }}</p>

    <!-- Modal for Category Search -->
    <div v-if="modalOpen" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <h3 class="modal-title">Search for a Category</h3>
        <CategoryFilter
          :categories="categories"
          v-model="selectedCategoryFromModal"
          :isFromProductForm="false"
          @change="handleModalCategoryChange"
        />
        <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useProductStore } from '@/stores/product';
import { useCategoryStore } from '@/stores/category';
import type { Category } from '@/types/category';
import CategoryCheckbox from './CategoryCheckbox.vue';
import CategoryFilter from './category/CategoryFilter.vue';

const categoryStore = useCategoryStore();
const productStore = useProductStore();

const name = ref<string>('');
const description = ref<string>('');
const price = ref<string>('');
const uploadMethod = ref<'file' | 'url'>('file');
const imageFile = ref<File | null>(null);
const imageUrl = ref<string>('');

// Selected categories (from checkboxes)
const selectedCategories = ref<string[]>([]);

// For the modal search (single category selection)
const modalOpen = ref(false);
const selectedCategoryFromModal = ref<string>('');

const message = ref<string>('');

// Get the categories from the category store
const categories = computed<Category[]>(() => categoryStore.categories);

// Compute a preview URL for the image if a file is chosen
const imagePreview = computed(() => {
  if (uploadMethod.value === 'file' && imageFile.value) {
    return URL.createObjectURL(imageFile.value);
  } else if (uploadMethod.value === 'url') {
    return imageUrl.value;
  }
  return '';
});

// File upload handler
function handleFileUpload(event: Event): void {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    imageFile.value = target.files[0];
  }
}

function removeFile(): void {
  imageFile.value = null;
  const fileInput = document.getElementById('fileInput') as HTMLInputElement;
  if (fileInput) {
    fileInput.value = '';
  }
}

function removeUrl(): void {
  imageUrl.value = '';
  const urlInput = document.getElementById('urlInput') as HTMLInputElement;
  if (urlInput) {
    urlInput.value = '';
  }
}

// Handle form submission: include the image and categories
async function handleSubmit(): Promise<void> {
  let imageData: string | File | undefined = undefined;

  if (uploadMethod.value === 'file' && imageFile.value) {
    imageData = imageFile.value;
  } else if (uploadMethod.value === 'url' && imageUrl.value) {
    imageData = imageUrl.value;
  }

  // If a category was selected via modal, add it if not already included.
  if (selectedCategoryFromModal.value && !selectedCategories.value.includes(selectedCategoryFromModal.value)) {
    selectedCategories.value.push(selectedCategoryFromModal.value);
  }

  const newProduct = {
    name: name.value,
    description: description.value,
    price: parseFloat(price.value),
    image: imageData,
    categories: selectedCategories.value,
  };

  productStore.addProduct(newProduct);

  // Clear the form
  name.value = '';
  description.value = '';
  price.value = '';
  imageFile.value = null;
  imageUrl.value = '';
  selectedCategories.value = [];
  selectedCategoryFromModal.value = '';
  message.value = 'Product added successfully!';
}

// --- Modal methods for category search ---
function openModal(): void {
  modalOpen.value = true;
}

function closeModal(): void {
  modalOpen.value = false;
}

function handleModalCategoryChange(categoryId: string): void {
  selectedCategoryFromModal.value = categoryId;
  // Optionally add the selected category to the list if not already there.
  if (categoryId && !selectedCategories.value.includes(categoryId)) {
    selectedCategories.value.push(categoryId);
  }
  closeModal();
}

function clearSelectedCategoryFromModal(): void {
  selectedCategoryFromModal.value = '';
}

function getCategoryName(categoryId: string): string {
  const cat = categories.value.find((c) => c.id === categoryId);
  return cat ? cat.name : '';
}

onMounted(() => {
  categoryStore.fetchCategories();
});
</script>

<style scoped>
/* Card and form styles (same as before) */
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
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-family: 'Poppins', sans-serif;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.radio-group {
  display: flex;
  gap: 1rem;
}

.file-upload {
  position: relative;
  border: 1px dashed #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s;
}

.file-upload:hover {
  border-color: #3b82f6;
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
  color: #6b7280;
  font-family: 'Poppins', sans-serif;
}

.url-upload {
  text-align: center;
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
  border: 1px solid #e5e7eb;
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

.btn-info {
  background-color: #17a2b8;
  color: #fff;
  transition: background-color 0.2s;
}

.btn-info:hover {
  background-color: #138496;
}

.btn-secondary {
  background-color: #6c757d;
  color: #fff;
}

.message {
  margin-top: 1rem;
  color: green;
  font-family: 'Poppins', sans-serif;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
}

.modal-content {
  background: white;
  padding: 1.5rem;
  border-radius: 0.5rem;
  width: 90%;
  max-width: 500px;
  position: relative;
}

.modal-title {
  margin-bottom: 1rem;
}
</style>
