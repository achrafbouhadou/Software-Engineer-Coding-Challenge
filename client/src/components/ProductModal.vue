<template>
  <div class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Create Product</h2>
        <button class="close-button" @click="closeModal">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="onSubmit">
          <div class="form-group">
            <label for="name">Name:</label>
            <input id="name" v-model="product.name" type="text" />
            <span v-if="errors.name" class="error">{{ errors.name }}</span>
          </div>

          <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" v-model="product.description"></textarea>
            <span v-if="errors.description" class="error">{{ errors.description }}</span>
          </div>

          <div class="form-group">
            <label for="price">Price:</label>
            <input id="price" v-model.number="product.price" type="number" step="0.01" />
            <span v-if="errors.price" class="error">{{ errors.price }}</span>
          </div>

          <div class="form-group">
            <label for="image">Image:</label>
            <input id="image" @change="onFileChange" type="file" accept="image/*" />
            <span v-if="errors.image" class="error">{{ errors.image }}</span>
          </div>

          <div class="form-group">
            <label for="categories">Categories (comma separated):</label>
            <input id="categories" v-model="categoriesInput" type="text" />
            <span v-if="errors.categories" class="error">{{ errors.categories }}</span>
          </div>

          <div class="modal-footer">
            <button type="submit" class="submit-button">Create Product</button>
            <button type="button" class="cancel-button" @click="closeModal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, ref } from 'vue';
import { useProductStore, Product } from '../stores/productStore';

export default defineComponent({
  name: 'ProductModal',
  emits: ['close'],
  setup(_, { emit }) {
    const productStore = useProductStore();

    // Reactive product model
    const product = reactive<Product>({
      id: Date.now(),
      name: '',
      description: '',
      price: 0,
      image: '',
      categories: [],
      created_at: new Date(),
      updated_at: new Date(),
    });

    // Temporary input for comma-separated categories
    const categoriesInput = ref('');

    // File input reference
    const fileInput = ref<File | null>(null);

    // Validation errors
    const errors = reactive({
      name: '',
      description: '',
      price: '',
      image: '',
      categories: '',
    });

    const validate = (): boolean => {
      let valid = true;
      errors.name = '';
      errors.description = '';
      errors.price = '';
      errors.image = '';
      errors.categories = '';

      if (!product.name.trim()) {
        errors.name = 'Name is required.';
        valid = false;
      }
      if (!product.description.trim()) {
        errors.description = 'Description is required.';
        valid = false;
      }
      if (isNaN(product.price) || product.price <= 0) {
        errors.price = 'Price must be a positive number.';
        valid = false;
      }
      if (!fileInput.value) {
        errors.image = 'Image is required.';
        valid = false;
      }
      if (!categoriesInput.value.trim()) {
        errors.categories = 'At least one category is required.';
        valid = false;
      }
      return valid;
    };

    const onFileChange = (event: Event) => {
      const target = event.target as HTMLInputElement;
      if (target.files && target.files[0]) {
        fileInput.value = target.files[0];
        product.image = URL.createObjectURL(target.files[0]);
      }
    };

    const onSubmit = () => {
      if (validate()) {
        product.categories = categoriesInput.value
          .split(',')
          .map(c => c.trim())
          .filter(c => c !== '');
        product.created_at = new Date();
        product.updated_at = new Date();

        // Clone and add the new product to the store.
        const newProduct: Product = { ...product };
        productStore.addProduct(newProduct);

        // Reset form fields.
        product.name = '';
        product.description = '';
        product.price = 0;
        product.image = '';
        product.categories = [];
        categoriesInput.value = '';
        fileInput.value = null;

        // Close modal after product creation.
        emit('close');
      }
    };

    const closeModal = () => {
      emit('close');
    };

    return {
      product,
      categoriesInput,
      errors,
      onFileChange,
      onSubmit,
      closeModal,
    };
  },
});
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  width: 500px;
  border-radius: 5px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #007bff;
  color: #fff;
  padding: 10px 15px;
}

.close-button {
  background: none;
  border: none;
  font-size: 24px;
  line-height: 1;
  color: #fff;
  cursor: pointer;
}

.modal-body {
  padding: 15px;
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}

input,
textarea {
  width: 100%;
  padding: 8px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.error {
  color: red;
  font-size: 12px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.submit-button, .cancel-button {
  padding: 8px 12px;
  border: none;
  font-size: 14px;
  border-radius: 4px;
  cursor: pointer;
}

.submit-button {
  background-color: #28a745;
  color: #fff;
}

.submit-button:hover {
  background-color: #218838;
}

.cancel-button {
  background-color: #dc3545;
  color: #fff;
}

.cancel-button:hover {
  background-color: #c82333;
}
</style>
