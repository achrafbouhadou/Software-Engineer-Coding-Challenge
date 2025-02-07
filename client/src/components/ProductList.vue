<template>
  <div class="product-list">
    <h2 class="text-2xl font-poppins font-semibold mb-8">Product List</h2>
    
    <div class="filter-container">
      <div class="filter-group">
        <label class="filter-label" for="categoryFilter">Filter by Category</label>
        <div class="select-wrapper">
          <select id="categoryFilter" v-model="selectedCategory" class="custom-select">
            <option value="">All</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
      </div>

      <div class="filter-group">
        <label class="filter-label" for="sortOrder">Sort by Price</label>
        <div class="select-wrapper">
          <select id="sortOrder" v-model="sortOrder" class="custom-select">
            <option value="asc">Ascending</option>
            <option value="desc">Descending</option>
          </select>
        </div>
      </div>
    </div>

    <div class="products-grid">
      <div v-for="product in sortedFilteredProducts" :key="product.id" class="product-card">
        <div class="product-image">
          <img :src="product.image" alt="Product Image" />
        </div>
        <div class="product-content">
          <h3 class="product-title">{{ product.name }}</h3>
          <p class="product-description">{{ product.description }}</p>
          <p class="product-price">${{ product.price.toLocaleString() }}</p>
          <div class="product-categories">
            <span v-for="catId in product.categories" :key="catId" class="category-tag">
              {{ getCategoryName(catId) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useDataStore } from '../stores/dataStore';
import type { Product, Category } from '../stores/dataStore';

const store = useDataStore();

const selectedCategory = ref<string | number>('');
const sortOrder = ref<'asc' | 'desc'>('asc');

const categories = computed<Category[]>(() => store.categories);
const products = computed<Product[]>(() => store.products);

const sortedFilteredProducts = computed<Product[]>(() => {
  let filtered = products.value;
  if (selectedCategory.value !== '') {
    const catId =
      typeof selectedCategory.value === 'number'
        ? selectedCategory.value
        : parseInt(selectedCategory.value as string);
    filtered = filtered.filter(product => product.categories.includes(catId));
  }
  filtered = filtered.slice().sort((a, b) => {
    return sortOrder.value === 'asc' ? a.price - b.price : b.price - a.price;
  });
  return filtered;
});

function getCategoryName(catId: number): string {
  const category = store.categories.find(cat => cat.id === catId);
  return category ? category.name : '';
}
</script>

<style scoped>

.product-list {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
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
