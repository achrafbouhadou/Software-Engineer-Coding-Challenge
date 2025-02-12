import { defineStore } from 'pinia';
import type { ProductPayload , Product } from '@/types/product';
import { createProduct, getProducts , searchProducts as searchProductsService } from '@/services/productService';



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
    async fetchProducts(sortBy: "asc" | "desc" | null = null, filterCategory: string | null = null): Promise<void> {
      try {
        const response = await getProducts(sortBy, filterCategory);
        const data : Product[] = response.data;
        this.products = data;
      } catch (error) {
        console.error('Error fetching products', error);
      }
    },
    async searchProducts(query: string): Promise<void> {
      try {
        const response = await searchProductsService(query);
        const hits = response.data.hits;
        this.products = hits.map((hit: any) => {
          return {
            id: hit._id,
            ...hit._source,
          } as Product;
        });
      } catch (error) {
        console.error('Error searching products', error);
      }
    },
  },
});
