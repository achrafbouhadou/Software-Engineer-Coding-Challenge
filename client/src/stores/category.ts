import { defineStore } from 'pinia';
import { createCategory, getCategories , searchCategories as searchCategoriesService } from '@/services/categoryService';
import type { Category ,CategoryPayload } from '@/types/category';



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
        if (this.categories.length > 0) {
          return; // Prevent unnecessary API call
        }    
        const response = await getCategories();
        const data : Category[] = response.data;
        this.categories = data;
      } catch (error) {
        console.error('Error fetching categories', error);
      }
    },
    async searchCategories(query: string): Promise<void> {
      try {
        const response = await searchCategoriesService(query);
        
        const hits = response.data.hits;
        this.categories = hits.map((hit: any) => {
          return {
            id: hit._id,
            ...hit._source,
          } as Category;
        });
      } catch (error) {
        console.error('Error searching categories', error);
      }
    },
  },
});
