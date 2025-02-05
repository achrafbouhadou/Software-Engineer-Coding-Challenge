import axiosClient from './axiosClient';
 
import type ProductPayload from '@/types/productPayload';


export const createProduct = async (payload: ProductPayload): Promise<any> => {
  try {
    const formData = new FormData();
    formData.append('name', payload.name);
    formData.append('description', payload.description);
    formData.append('price', payload.price.toString());

    if (payload.image) {
      if (payload.image instanceof File) {
        formData.append('image', payload.image);
      } else {
        formData.append('image', payload.image);
      }
    }

    payload.categories.forEach((category) => {
      formData.append('categories[]', category);
    });

    const response = await axiosClient.post('api/products', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  } catch (error) {
    throw error;
  }
};
