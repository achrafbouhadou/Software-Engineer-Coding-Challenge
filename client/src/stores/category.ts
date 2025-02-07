import { defineStore } from 'pinia';
import { createCategory, getCategories } from '@/services/categoryService';
import type { Category ,CategoryPayload } from '@/types/Category';



interface CategoryState {
  categories: Category[];
}

export const useCategoryStore = defineStore('category', {
  state: (): CategoryState => ({
    categories: [],
  }),
  actions: {
    async addCategory(payload: CategoryPayload): Promise<void> {
      try {
        const response = await createCategory(payload);
        const newCategory: Category = response.data;
        this.categories.push(newCategory);
      } catch (error) {
        console.error('Error adding category', error);
      }
    },

    async fetchCategories(): Promise<void> {
      try {
        const response = await getCategories();
        const data : Category[] = response.data;
        this.categories = data;
      } catch (error) {
        console.error('Error fetching categories', error);
      }
    },
  },
});
