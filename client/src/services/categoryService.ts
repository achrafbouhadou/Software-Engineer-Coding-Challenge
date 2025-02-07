import axiosClient from './axiosClient';
import type {CategoryPayload}  from '@/types/category';

export const createCategory = async (payload: CategoryPayload): Promise<any> => {
  try {
    const formData = new FormData();
    formData.append('name', payload.name);
    if (payload.parent_id) {
      formData.append('parent_id', payload.parent_id);
    }

    const response = await axiosClient.post('api/v1/categories', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data; 
  } catch (error) {
    throw error;
  }
};

export const getCategories = async (): Promise<any> => {
  try {
    const response = await axiosClient.get('api/v1/categories');
    return response.data; 
  } catch (error) {
    throw error;
  }
};
