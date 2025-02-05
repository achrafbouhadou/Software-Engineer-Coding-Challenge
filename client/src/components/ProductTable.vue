<template>
  <div class="product-table">
    <div class="table-controls">
      <div class="control-group">
        <label for="sort">Sort by Price:</label>
        <select id="sort" v-model="sortBy">
          <option value="">None</option>
          <option value="price">Price (Low to High)</option>
        </select>
      </div>
      <div class="control-group">
        <label for="filter">Filter by Category:</label>
        <input id="filter" type="text" v-model="filterCategory" placeholder="Category name" />
      </div>
    </div>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Price</th>
          <th>Categories</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in filteredProducts" :key="product.id">
          <td>{{ product.name }}</td>
          <td>{{ product.description }}</td>
          <td>${{ product.price.toFixed(2) }}</td>
          <td>{{ product.categories.join(', ') }}</td>
          <td>{{ new Date(product.created_at).toLocaleString() }}</td>
        </tr>
        <tr v-if="filteredProducts.length === 0">
          <td colspan="5" class="no-data">No products found.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import { useProductStore } from '../stores/productStore';

export default defineComponent({
  name: 'ProductTable',
  setup() {
    const productStore = useProductStore();
    const sortBy = ref<string>('');
    const filterCategory = ref<string>('');

    const filteredProducts = computed(() => {
      return productStore.fetchProducts(
        sortBy.value === 'price' ? 'price' : null,
        filterCategory.value.trim() || null
      );
    });

    return {
      sortBy,
      filterCategory,
      filteredProducts,
    };
  },
});
</script>

<style scoped>
.product-table {
  margin: 20px;
}

.table-controls {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  gap: 20px;
  margin-bottom: 10px;
}

.control-group {
  display: flex;
  flex-direction: column;
}

.control-group label {
  font-weight: bold;
  margin-bottom: 5px;
}

.control-group input,
.control-group select {
  padding: 6px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: left;
}

th {
  background-color: #f8f8f8;
}

.no-data {
  text-align: center;
  font-style: italic;
  color: #777;
}
</style>
