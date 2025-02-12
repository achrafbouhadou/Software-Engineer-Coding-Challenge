<template>
  <div class="product-list">
    <h2 class="text-2xl font-poppins font-semibold mb-8">Product List</h2>

    <div class="search-container mb-6">
      <input
        type="text"
        v-model="searchQuery"
        @input="handleSearch"
        placeholder="Search products or category..."
        class="search-input"
      />
    </div>
    
    <div class="filter-container">
      <!-- Category Filter Group -->
      <div class="filter-group">
        <div class="filter-group">
          <label class="filter-label">Filter by Category</label>
            <CategoryFilter
              v-model="selectedCategory"
              :categories="formattedCategories"
              @change="applyFilter"
            />
        </div>

      </div>

      <div class="filter-group">
        <label class="filter-label" for="sortOrder">Sort by Price</label>
        <div class="select-wrapper">
          <select id="sortOrder" v-model="sortOrder" @change="applyFilter" class="custom-select">  
            <option value="asc">Ascending</option>
            <option value="desc">Descending</option>
          </select>
        </div>
      </div>
    </div>

    <div class="products-grid">
      <div v-for="product in products" :key="product.id" class="product-card">
        <div class="product-image">
          <img :src="product.image || defaultImage" alt="Product Image" />
        </div>
        <div class="product-content">
          <h3 class="product-title">{{ product.name }}</h3>
          <p class="product-description">{{ product.description }}</p>
          <p class="product-price">${{ product.price }}</p>
          <div class="product-categories">
            <span v-for="cat in product.categories" :key="cat.id" class="category-tag">
              {{ cat.name }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useProductStore } from '../stores/product';
import type { Product } from '../types/product';
import type { Category } from '@/types/category';
import { useCategoryStore } from '@/stores/category';
import CategoryFilter from './category/CategoryFilter.vue';

const productStore = useProductStore();
const categoryStore = useCategoryStore();

// Product search & filter state
const selectedCategory = ref<string>('');
const sortOrder = ref<'asc' | 'desc'>('asc');
const searchQuery = ref<string>('');


const defaultImage = 'https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg';

const products = computed<Product[]>(() => productStore.products);

const categories = computed<Category[]>(() => categoryStore.categories);
const formattedCategories = computed<Category[]>(() => {
  return flattenCategories(categories.value);
});

// Helper function to flatten nested categories (if applicable)
function flattenCategories(categories: Category[]): Category[] {
  let flatList: Category[] = [];
  categories.forEach(category => {
    flatList.push({
      id: category.id,
      name: category.name,
      parent_id: category.parent_id,
      created_at: category.created_at,
      updated_at: category.updated_at
    });
    if (category.children && category.children.length > 0) {
      flatList = flatList.concat(flattenCategories(category.children));
    }
  });
  return flatList;
}

const applyFilter = (): void => {
  searchQuery.value = '';
  productStore.fetchProducts(sortOrder.value, selectedCategory.value);
};

const handleSearch = (): void => {
  if (searchQuery.value.trim() === '') {
    productStore.fetchProducts(sortOrder.value, selectedCategory.value);
  } else {
    productStore.searchProducts(searchQuery.value);
  }
};

onMounted(() => {
  productStore.fetchProducts();
  categoryStore.fetchCategories();
});
</script>

<style scoped>
.product-list {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.search-container {
  margin-bottom: 1.5rem;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #E5E7EB;
  border-radius: 0.5rem;
  font-family: 'Poppins', sans-serif;
  font-size: 0.875rem;
  color: #374151;
}

.filter-container {
  display: flex;
  gap: 2rem;
  margin-bottom: 2.5rem;
  flex-wrap: wrap;
}

.filter-group {
  flex: 1;
  min-width: 200px;
}

.filter-label {
  display: block;
  margin-bottom: 0.5rem;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 500;
}

.select-wrapper {
  position: relative;
}

.select-wrapper::after {
  content: '▼';
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  color: #6B7280;
  font-size: 0.75rem;
}

.custom-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #E5E7EB;
  border-radius: 0.5rem;
  background-color: white;
  font-family: 'Poppins', sans-serif;
  font-size: 0.875rem;
  color: #374151;
  appearance: none;
  cursor: pointer;
  transition: all 0.2s;
}

.custom-select:hover {
  border-color: #9CA3AF;
}

.custom-select:focus {
  outline: none;
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.product-card {
  background: white;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transition: transform 0.2s;
}

.product-card:hover {
  transform: translateY(-4px);
}

.product-image {
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-content {
  padding: 1.5rem;
}

.product-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.5rem;
}

.product-description {
  color: #6B7280;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 600;
  color: #3B82F6;
  margin-bottom: 1rem;
}

.product-categories {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.category-tag {
  background-color: #F3F4F6;
  color: #374151;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}
</style>
