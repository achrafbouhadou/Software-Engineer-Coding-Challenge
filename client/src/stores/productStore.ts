import { defineStore } from 'pinia';

export interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  image?: string; 
  categories: string[]; 
  created_at: Date;
  updated_at: Date;
}

interface ProductState {
  products: Product[];
}

export const useProductStore = defineStore('product', {
  state: (): ProductState => ({
    products: [],
  }),
  actions: {
    addProduct(product: Product) {
    },
   
    fetchProducts(sortBy: 'price' | null, filterCategory: string | null): Product[] {
      return []
    },
  },
});
