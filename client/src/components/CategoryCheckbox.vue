<template>
  <div class="category-checkbox">
    <label class="checkbox-label">
      <input
        type="checkbox"
        :value="category.id"
        :checked="isChecked"
        @change="toggleCategory"
        class="checkbox-input"
      />
      <span class="checkbox-text">{{ category.name }}</span>
    </label>
    <div v-if="category.children && category.children.length" class="children">
      <CategoryCheckbox
        v-for="child in category.children"
        :key="child.id"
        :category="child"
        :checkedCategories="checkedCategories"
        @update:checkedCategories="updateCheckedCategories"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { Category } from '../stores/dataStore';
import CategoryCheckbox from './CategoryCheckbox.vue';

interface CategoryNode extends Category {
  children?: CategoryNode[];
}

interface Props {
  category: CategoryNode;
  checkedCategories: number[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:checkedCategories', value: number[]): void;
}>();

const isChecked = computed(() => props.checkedCategories.includes(props.category.id));

function toggleCategory(): void {
  let newChecked = [...props.checkedCategories];
  if (isChecked.value) {
    newChecked = newChecked.filter(id => id !== props.category.id);
  } else {
    newChecked.push(props.category.id);
  }
  emit('update:checkedCategories', newChecked);
}

function updateCheckedCategories(newChecked: number[]): void {
  emit('update:checkedCategories', newChecked);
}
</script>

<style scoped>
.category-checkbox {
  margin-left: 10px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  font-size: 0.875rem;
  color: #374151;
  cursor: pointer;
  font-family: 'Poppins', sans-serif;
}

.checkbox-input {
  margin-right: 0.5rem;
  width: 16px;
  height: 16px;
  accent-color: #3B82F6;
}

.children {
  margin-left: 20px;
  padding-left: 10px;
  border-left: 2px solid #E5E7EB;
}
</style>
