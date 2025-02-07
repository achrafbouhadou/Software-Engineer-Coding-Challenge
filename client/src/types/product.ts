import type { Category } from './category';
export interface ProductPayload {
  name: string;
  description: string;
  price: number;
  image?: File | string;
  categories: string[]; 
}
export interface Product {
  id: string;
  name: string;
  description: string;
  price: number;
  image?: string;
  categories: Category[];
  created_at: Date;
  updated_at: Date;
}