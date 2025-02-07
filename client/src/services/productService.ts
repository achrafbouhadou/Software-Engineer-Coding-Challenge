import axiosClient from './axiosClient';
import type { ProductPayload } from '@/types/product';

export const createProduct = async (payload: ProductPayload): Promise<any> => {
  try {
    const formData = new FormData();
    formData.append('name', payload.name);
    formData.append('description', payload.description);
    formData.append('price', payload.price.toString());

    if (payload.image) {
      const key = payload.image instanceof File ? 'image_file' : 'image';
      formData.append(key, payload.image);
    }

    payload.categories.forEach((category) => {
      formData.append('categories[]', category);
    });

    const response = await axiosClient.post('api/v1/products', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  } catch (error) {
    throw error;
  }
};

export const getProducts = async (
  sortBy: "asc" | "desc" | null,
  filterCategory: string | null
): Promise<any> => {
  try {
    const params: any = {};
    if (sortBy) {
      params.sortBy = sortBy;
    }
    if (filterCategory) {
      params.filterCategory = filterCategory;
    }
    const response = await axiosClient.get('api/v1/products', { params });
    return response.data;
  } catch (error) {
    throw error;
  }
};
