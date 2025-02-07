import { defineStore } from 'pinia';
import type { ProductPayload , Product } from '@/types/product';
import { createProduct, getProducts } from '@/services/productService';



interface ProductState {
  products: Product[];
}

export const useProductStore = defineStore('product', {
  state: (): ProductState => ({
    products: [],
  }),
  actions: {
    async addProduct(payload: ProductPayload): Promise<void> {
      try {
        const response = await createProduct(payload);
        const newProduct: Product = response.data;
        this.products.push(newProduct);
      } catch (error) {
        console.error('Error adding product', error);
      }
    },
    async fetchProducts(sortBy: 'price' | null = null, filterCategory: string | null = null): Promise<void> {
      try {
        const response = await getProducts(sortBy, filterCategory);
        const data : Product[] = response.data;
        this.products = data;
      } catch (error) {
        console.error('Error fetching products', error);
      }
    },
  },
});
