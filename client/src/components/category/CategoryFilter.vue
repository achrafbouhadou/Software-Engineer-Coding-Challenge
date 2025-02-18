<template>
    <div class="category-dropdown">
      <button 
        @click="toggleDropdown" 
        class="dropdown-btn"
        :class="{ 'active': isOpen }"
      >
        {{ selectedCategory ? selectedCategoryName : 'Select Category' }}
      </button>
      
      <div class="dropdown-content" :class="{ 'show': isOpen }">
        <div class="search-wrapper">
          <input
            type="text"
            v-model="searchQuery"
            @input="handleSearch"
            placeholder="Search categories..."
            class="dropdown-search"
          />
        </div>
        
        <div class="options-container">
          <div 
            
            class="dropdown-option"
            @click="selectCategory('')"
          >
            <span v-if="isFromProductForm">
                All Categories
            </span>
            <span v-else>
                None
            </span>
          </div>
          <div
            v-for="category in categories"
            :key="category.id"
            class="dropdown-option"
            @click="selectCategory(category.id)"
            :class="{ 'selected': category.id === selectedCategory }"
          >
            {{ category.name }}
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed, onMounted, onUnmounted } from 'vue';
  import type { Category } from '@/types/category';
  import { useCategoryStore } from '@/stores/category';
  
  const props = defineProps<{
    categories: Category[];
    modelValue: string;
    isFromProductForm?: boolean;
  }>();
  
  const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'change', value: string): void;
  }>();
  
  // Local state for the dropdown
  const isOpen = ref(false);
  const searchQuery = ref('');
  
  const categoryStore = useCategoryStore();
  
  const searchResults = ref<Category[]>([]);
  
  const selectedCategory = computed(() => props.modelValue);
  
  // Compute the selected category name from the full list (provided via props)
  const selectedCategoryName = computed(() => {
    const category = props.categories.find(cat => cat.id === selectedCategory.value);
    return category ? category.name : 'Select Category';
  });
  
  // Call the backend to search categories
  const handleCategorySearch = async (): Promise<void> => {
    if (searchQuery.value.trim() === '') {
      searchResults.value = [];
    } else {
      try {
        categoryStore.searchCategories(searchQuery.value);
      } catch (error) {
        console.error('Error searching categories:', error);
      }
    }
  };
  
  // When the user types in the input, show the dropdown (if it isn’t already open)
  // and perform the search.
  const handleSearch = () => {
    if (!isOpen.value) {
      isOpen.value = true;
    }
    handleCategorySearch();
  };
  
  // Toggles the dropdown open/close
  const toggleDropdown = (e: Event) => {
    e.preventDefault();
    
    isOpen.value = !isOpen.value;
  };
  
  // When a category is selected, emit the change and clear the search.
  const selectCategory = (categoryId: string) => {
    emit('update:modelValue', categoryId);
    emit('change', categoryId);
    isOpen.value = false;
    searchQuery.value = '';
    searchResults.value = [];
  };
  
  // Close dropdown when clicking outside
  const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.category-dropdown')) {
      isOpen.value = false;
    }
  };
  
  onMounted(() => {
    document.addEventListener('click', handleClickOutside);
  });
  
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
  });
  </script>
  
  <style scoped>
  .category-dropdown {
    position: relative;
    width: 100%;
  }
  
  .dropdown-btn {
    width: 100%;
    padding: 0.75rem 1rem;
    background-color: white;
    border: 1px solid #E5E7EB;
    border-radius: 0.5rem;
    font-family: 'Poppins', sans-serif;
    font-size: 0.875rem;
    color: #374151;
    text-align: left;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
  }
  
  .dropdown-btn::after {
    content: '▼';
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #6B7280;
    font-size: 0.75rem;
  }
  
  .dropdown-btn:hover {
    border-color: #9CA3AF;
  }
  
  .dropdown-btn.active {
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  .dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    margin-top: 0.5rem;
    background-color: white;
    border: 1px solid #E5E7EB;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    z-index: 50;
  }
  
  .dropdown-content.show {
    display: block;
  }
  
  .search-wrapper {
    padding: 0.75rem;
    border-bottom: 1px solid #E5E7EB;
  }
  
  .dropdown-search {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #E5E7EB;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    color: #374151;
  }
  
  .dropdown-search:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  .options-container {
    max-height: 250px;
    overflow-y: auto;
  }
  
  .dropdown-option {
    padding: 0.75rem 1rem;
    cursor: pointer;
    font-size: 0.875rem;
    color: #374151;
    transition: background-color 0.2s;
  }
  
  .dropdown-option:hover {
    background-color: #F3F4F6;
  }
  
  .dropdown-option.selected {
    background-color: #EFF6FF;
    color: #3B82F6;
  }
  </style>
  